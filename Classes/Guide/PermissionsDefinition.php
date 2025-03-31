<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\Guide;

final readonly class PermissionsDefinition implements DefinitionInterface
{
    public function __construct(
        public array $availableWidgets = [],
        public array $modules = [],
        public array $pagetypesSelect = [],
        public array $tablesModify = [],
        public array $tablesSelect = [],
        public array $nonExcludeFields = [],
        public array $explicitAllowdeny = [],
        public array $filePermissions = []
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), static fn(mixed $value) => $value !== []);
    }

    public static function __set_state(array $state): self
    {
        return new self(...$state);
    }
}
