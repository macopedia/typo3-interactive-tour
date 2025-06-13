<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

$lll = 'LLL:EXT:typo3_interactive_tour/Resources/Private/Language/locallang_db.xlf:';

$GLOBALS['TYPO3_USER_SETTINGS']['columns']['disableTours'] = [
    'label' => $lll . 'be_users.disableTours',
    'type' => 'check',
];
ExtensionManagementUtility::addFieldsToUserSettings(
    'disableTours',
    'after:backendTitleFormat',
);
