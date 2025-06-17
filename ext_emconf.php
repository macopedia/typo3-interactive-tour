<?php

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'An interactive tour through the TYPO3 backend',
    'description' => '',
    'category' => 'be',
    'state' => 'stable',
    'author' => 'Macopedia Dev Team',
    'author_email' => 'extensions@macopedia.pl',
    'author_company' => 'Macopedia Sp. z o.o.',
    'version' => '1.0.2',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
