<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\Storage;

use Macopedia\Typo3InteractiveTour\Guide\Tour;

interface TourStorageInterface
{
    public function getTourConfiguration(string $tourIdentifier): array;

    public function markTourAsCompleted(Tour $tour): void;

    public function markTourAsNotCompleted(Tour $tour): void;

    public function setTourAsEnabled(Tour $tour): void;

    public function setTourAsDisabled(Tour $tour): void;
}
