<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\Guide;

final readonly class EventDefinition implements DefinitionInterface
{
    public function __construct(
        public string $event,
        public string $target,
        public ?string $frame = null,
        public int $delayBefore = 0,
        public int $delayAfter = 0
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), static fn(mixed $value) => $value !== null && $value !== 0);
    }

    public static function __set_state(array $state): self
    {
        return new self(...$state);
    }
}
