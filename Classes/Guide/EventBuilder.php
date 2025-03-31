<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\Guide;

final class EventBuilder
{
    /**
     * @param EventDefinition $eventDefinition
     * @return array<string, mixed>
     */
    public function createFromDefinition(EventDefinition $eventDefinition): array
    {
        return $eventDefinition->toArray();
    }
}
