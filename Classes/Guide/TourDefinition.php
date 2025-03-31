<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\Guide;

final readonly class TourDefinition implements DefinitionInterface
{
    /**
     * @param StepDefinition[] $steps
     */
    public function __construct(
        public string $identifier,
        public string $title,
        public string $description,
        public ?PermissionsDefinition $permissions = null,
        public ?string $moduleName = null,
        public bool $isStandalone = false,
        public ?string $skipButtonText = null,
        public ?string $previousButtonText = null,
        public ?string $nextButtonText = null,
        public ?string $finishButtonText = null,
        public ?string $nextTourIdentifier = null,
        public array $steps = []
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), static fn(mixed $value) => $value !== null && $value !== false && $value !== [] && $value !== '');
    }

    public static function __set_state(array $state): self
    {
        return new self(...$state);
    }
}
