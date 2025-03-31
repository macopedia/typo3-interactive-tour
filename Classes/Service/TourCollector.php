<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\Service;

use Macopedia\Typo3InteractiveTour\Guide\EventDefinition;
use Macopedia\Typo3InteractiveTour\Guide\PermissionsDefinition;
use Macopedia\Typo3InteractiveTour\Guide\StepDefinition;
use Macopedia\Typo3InteractiveTour\Guide\TourDefinition;
use Macopedia\Typo3InteractiveTour\Event\TourConfigurationLoadedEvent;
use Macopedia\Typo3InteractiveTour\Exception\NoUniqueIdentifierException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;
use TYPO3\CMS\Backend\Module\ModuleProvider;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Package\PackageInterface;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Tour Collector role is to collect all tours configuration from all packages,
 * store them in cache and provide them to other services as an array (all or single tour).
 */
class TourCollector
{
    private const CACHE_IDENTIFIER = 'guide-tours';

    public function __construct(
        protected EventDispatcher $eventDispatcher,
        #[Autowire(service: 'cache.core')]
        protected FrontendInterface $coreCache,
        protected ModuleProvider $moduleProvider,
        protected PackageManager $packageManager,
        protected YamlFileLoader $yamlFileLoader
    ) {
        $this->collectTours();
    }

    /**
     * @return TourDefinition[]
     */
    public function getTours(): array
    {
        return $this->collectTours();
    }

    /**
     * @return TourDefinition[]
     */
    public function getToursAccessibleByUser(): array
    {
        $toursDefinitions = array_filter(
            $this->collectTours(),
            function ($tourDefinition) {
                if (empty($tourDefinition->permissions)) {
                    return true;
                }

                foreach ($tourDefinition->permissions->toArray() as $permission => $values) {
                    foreach ($values as $value) {
                        $check = $this->getBackendUser()->check(GeneralUtility::camelCaseToLowerCaseUnderscored($permission), $value);
                        if (!$check) {
                            return false;
                        }
                    }
                }
                return true;
            }
        );
        return array_filter($toursDefinitions, fn(TourDefinition $tourDefinition) => empty($tourDefinition->moduleName) || $this->moduleProvider->accessGranted($tourDefinition->moduleName, $this->getBackendUser()));
    }

    public function getTourDefinitionByIdentifier(string $identifier): TourDefinition
    {
        $tour = $this->getToursAccessibleByUser()[$identifier] ?? null;

        if ($tour === null) {
            throw new \RuntimeException('Tour with identifier "' . $identifier . '" is not configured or not accessible by current user.', 1620140404);
        }

        return $tour;
    }

    public function isTourConfigured(string $identifier): bool
    {
        return isset($this->getTours()[$identifier]);
    }

    /**
     * @return TourDefinition[]
     * @throws NoUniqueIdentifierException
     */
    private function collectTours(): array
    {
        $tours = $this->coreCache->require(self::CACHE_IDENTIFIER);
        if ($tours && is_array($tours)) {
            return $tours;
        }

        $tours = [];
        foreach ($this->packageManager->getAvailablePackages() as $package) {
            $toursFromPackage = $this->getAllToursConfigurationFromPackage($package);
            if (empty($toursFromPackage)) {
                continue;
            }

            foreach ($toursFromPackage as $tourIdentifier => $tourConfiguration) {
                if (array_key_exists($tourIdentifier, $tours)) {
                    throw new NoUniqueIdentifierException(sprintf('Tour identifier "%s" is not unique.', $tourIdentifier), 1620140403);
                }
                $tours[$tourIdentifier] = $tourConfiguration;
            }
        }

        $this->coreCache->set(self::CACHE_IDENTIFIER, 'return ' . var_export($tours, true) . ';');

        return $tours;
    }

    private function getAllToursConfigurationFromPackage(PackageInterface $package): array
    {
        $finder = new Finder();
        try {
            $finder
                ->files()
                ->depth(0)
                ->name('*.yaml')
                ->sortByName()
                ->ignoreUnreadableDirs()
                ->in($package->getPackagePath() . 'Configuration/Tours');
        } catch (\InvalidArgumentException $e) {
            // The directory with tours configuration does not exist
            $finder = [];
        }
        $toursDefinitions = [];
        foreach ($finder as $fileInfo) {
            $configuration = $this->yamlFileLoader->load(GeneralUtility::fixWindowsFilePath((string)$fileInfo));
            $tourIdentifier = $configuration['identifier'];
            $event = $this->eventDispatcher->dispatch(new TourConfigurationLoadedEvent($tourIdentifier, $configuration));
            $configuration = $event->getConfiguration();

            $this->validateTourConfiguration($configuration);

            if (isset($configuration['permissions'])) {
                $configuration['permissions'] = new PermissionsDefinition(...$configuration['permissions']);
            }
            // create steps definitions
            $steps = [];
            foreach ($configuration['steps'] ?? [] as $stepConfiguration) {
                $stepEvents = [];
                foreach ($stepConfiguration['events'] ?? [] as $eventGroup => $eventsArray) {
                    foreach ($eventsArray as $eventConfiguration) {
                        $stepEvents[$eventGroup][] = new EventDefinition(...$eventConfiguration);
                    }
                }

                $stepConfiguration['events'] = $stepEvents;
                $steps[] = new StepDefinition(...$stepConfiguration);
            }
            if (!empty($steps)) {
                $configuration['steps'] = $steps;
            }
            $tourDefinition = new TourDefinition(...$configuration);
            $toursDefinitions[$tourIdentifier] = $tourDefinition;
        }

        return $toursDefinitions;
    }

    private function validateTourConfiguration(array $configuration): void
    {
        foreach ($configuration['steps'] ?? [] as $stepConfiguration) {
            foreach ($stepConfiguration['events'] ?? [] as $eventGroup => $eventConfiguration) {
                if (!in_array($eventGroup, ['onShow', 'onExit', 'userActions'], true)) {
                    throw new \RuntimeException('Invalid event group "' . $eventGroup . '" in tour configuration.', 1620140406);
                }
            }
        }
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
