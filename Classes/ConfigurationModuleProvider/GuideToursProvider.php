<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\ConfigurationModuleProvider;

use Macopedia\Typo3InteractiveTour\Guide\TourBuilder;
use Macopedia\Typo3InteractiveTour\Service\TourCollector;
use TYPO3\CMS\Lowlevel\ConfigurationModuleProvider\AbstractProvider;

class GuideToursProvider extends AbstractProvider
{
    public function __construct(
        private readonly TourCollector $tourCollector,
        private readonly TourBuilder $tourBuilder
    ) {}

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getConfiguration(): array
    {
        $toursDefinitions = $this->tourCollector->getTours();

        $toursConfigurations = [];

        foreach ($toursDefinitions as $tourDefinition) {
            $toursConfigurations[$tourDefinition->identifier] = $this->tourBuilder->createFromDefinition($tourDefinition);
        }

        return $toursConfigurations;
    }
}
