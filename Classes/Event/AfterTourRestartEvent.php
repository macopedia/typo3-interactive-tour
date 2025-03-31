<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\Event;

use Macopedia\Typo3InteractiveTour\Guide\Tour;

final class AfterTourRestartEvent
{
    public function __construct(protected Tour $tour) {}

    public function getTour(): Tour
    {
        return $this->tour;
    }
}
