<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\Guide;

use TYPO3\CMS\Core\Localization\LanguageService;

final readonly class StepBuilder
{
    public function __construct(private EventBuilder $eventBuilder) {}

    /**
     * @param StepDefinition $stepDefinition
     * @return array<string, mixed>
     */
    public function createFromDefinition(StepDefinition $stepDefinition): array
    {
        $stepConfiguration = $stepDefinition->toArray();

        $this->buildEvents($stepConfiguration);
        $this->applyTranslations($stepConfiguration);

        return $stepConfiguration;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function buildEvents(array &$configuration): void
    {
        if (empty($configuration['events'])) {
            return;
        }

        $groupedInstances = [];
        foreach ($configuration['events'] as $eventGroup => $eventDefinitions) {
            foreach ($eventDefinitions as $eventDefinition) {
                $groupedInstances[$eventGroup][] = $this->eventBuilder->createFromDefinition($eventDefinition);
            }
        }

        $configuration['events'] = $groupedInstances;
    }

    private function applyTranslations(array &$configuration): void
    {
        $configuration['title'] = $this->getLanguageService()->sL($configuration['title']);
        $configuration['content'] = $this->getLanguageService()->sL($configuration['content']);
        if (isset($configuration['previousButtonText'])) {
            $configuration['previousButtonText'] = $this->getLanguageService()->sL($configuration['previousButtonText']);
        }
        if (isset($configuration['nextButtonText'])) {
            $configuration['nextButtonText'] = $this->getLanguageService()->sL($configuration['nextButtonText']);
        }
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
