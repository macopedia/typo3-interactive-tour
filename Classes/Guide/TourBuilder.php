<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\Guide;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Localization\LanguageService;

final readonly class TourBuilder
{
    public function __construct(
        private StepBuilder $stepBuilder,
        private UriBuilder $uriBuilder
    ) {}

    /**
     * @param TourDefinition $tourDefinition
     * @return array<string, mixed>
     */
    public function createFromDefinition(TourDefinition $tourDefinition): array
    {
        $tourConfiguration = $tourDefinition->toArray();

        $this->setPermissions($tourConfiguration);
        $this->buildSteps($tourConfiguration);
        $this->applyTranslations($tourConfiguration);

        return $tourConfiguration;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function buildSteps(array &$configuration): void
    {
        if (empty($configuration['steps'])) {
            return;
        }

        $steps = [];
        foreach ($configuration['steps'] as $stepDefinition) {
            $steps[] = $this->stepBuilder->createFromDefinition($stepDefinition);
        }

        $configuration['steps'] = $steps;
    }

    private function applyTranslations(array &$configuration): void
    {
        $configuration['title'] = $this->getLanguageService()->sL($configuration['title']);
        $configuration['description'] = $this->getLanguageService()->sL($configuration['description']);
        if (isset($configuration['skipButtonText'])) {
            $configuration['skipButtonText'] = $this->getLanguageService()->sL($configuration['skipButtonText']);
        }
        if (isset($configuration['previousButtonText'])) {
            $configuration['previousButtonText'] = $this->getLanguageService()->sL($configuration['previousButtonText']);
        }
        if (isset($configuration['nextButtonText'])) {
            $configuration['nextButtonText'] = $this->getLanguageService()->sL($configuration['nextButtonText']);
        }
        if (isset($configuration['finishButtonText'])) {
            $configuration['finishButtonText'] = $this->getLanguageService()->sL($configuration['finishButtonText']);
        }
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    private function setPermissions(array &$tourConfiguration): void
    {
        if (is_a($tourConfiguration['permissions'] ?? null, PermissionsDefinition::class, true)) {
            $tourConfiguration['permissions'] = $tourConfiguration['permissions']->toArray();
        }
        if ($tourConfiguration['moduleName'] ?? false) {
            // will throw an exception if module route is not found
            $tourConfiguration['moduleRoute'] = $this->uriBuilder->buildUriFromRoute($tourConfiguration['moduleName'])->__toString();
        } else {
            $tourConfiguration['moduleRoute'] = null;
        }
    }
}
