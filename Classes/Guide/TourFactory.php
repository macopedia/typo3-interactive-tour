<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\Guide;

final readonly class TourFactory
{
    public function __construct(private TourBuilder $tourBuilder) {}

    public function createFromDefinition(TourDefinition $tourDefinition): Tour
    {
        $configuration = $this->tourBuilder->createFromDefinition($tourDefinition);
        return new Tour(...$configuration);
    }
}
