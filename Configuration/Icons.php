<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'module-guide' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:typo3_interactive_tour/Resources/Public/Icons/module-guide.svg',
    ],
];
