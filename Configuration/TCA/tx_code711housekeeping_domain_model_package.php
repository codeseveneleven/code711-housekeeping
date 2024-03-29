<?php

/*
 * This file is part of the TYPO3 project.
 * (c) 2024 B-Factor GmbH
 *          Sudhaus7
 *          12bis3
 *          Code711.de
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 * The TYPO3 project - inspiring people to share!
 * @copyright https://code711.de/
 *
 */

return [
    'ctrl' => [
        'title'	=> 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:package',
        'label' => 'title',
        'label_alt' => 'version, latest',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'default_sortby' => 'title',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'title,',
        'iconfile' => 'EXT:code711_housekeeping/Resources/Public/Icons/Extension.svg',
        'hideTable' => true,
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general, title, version, latest,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden
            ',
        ],
    ],
    'palettes' => [
        '' => ['showitem' => ''],
    ],
    'columns' => [
        'hidden' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'title' => [
            'label' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:package.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'version' => [
            'label' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:package.version',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'latest' => [
            'label' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:package.latest',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
    ],
];
