<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Macopedia\Typo3InteractiveTour\Controller\GuideController;

return [
    'help_guide' => [
        'parent' => 'help',
        'access' => 'user',
        'path' => '/module/help/guide',
        'iconIdentifier' => 'module-guide',
        'labels' => 'LLL:EXT:typo3_interactive_tour/Resources/Private/Language/locallang_mod.xlf',
        'routes' => [
            '_default' => [
                'target' => GuideController::class . '::overview',
            ],
        ],
    ],
];
