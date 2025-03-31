<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\Guide;

final readonly class StepDefinition implements DefinitionInterface
{
    /**
     * @param array<string, EventDefinition[]> $events
     */
    public function __construct(
        public string $title,
        public string $content,
        public ?string $target = null,
        public ?string $frame = null,
        public bool $enableInteraction = false,
        public ?string $side = null,
        public ?string $align = null,
        public ?string $previousButtonText = null,
        public ?string $nextButtonText = null,
        public bool $requireUserActions = false,
        public array $events = [],
        public int $sleep = 0
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), static fn(mixed $value) => $value !== null && $value !== false && $value !== [] && $value !== 0);
    }

    public static function __set_state(array $state): self
    {
        return new self(...$state);
    }
}
