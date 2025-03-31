<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\Widgets\Provider;

use TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\ElementAttributesInterface;

final class GuidesModuleButtonProvider implements ButtonProviderInterface, ElementAttributesInterface
{
    public function __construct(private readonly string $title, private readonly string $target = '') {}

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getLink(): string
    {
        return '';
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getElementAttributes(): array
    {
        return [
            'data-dispatch-action' => 'TYPO3.ModuleMenu.showModule',
            'data-dispatch-args-list' => 'help_guide',
        ];
    }
}
