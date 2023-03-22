<?php

/*
 * This file is part of the TYPO3 project.
 * (c) 2022 B-Factor GmbH / 12bis3 / Sudhaus7 / code711.de
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 * The TYPO3 project - inspiring people to share!
 *
 * @copyright 2022 B-Factor GmbH / 12bis3 / Sudhaus7 / https://code711.de/
 */

return [
    'ctrl' => [
        'title'	=> 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:group',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'title,',
        'iconfile' => 'EXT:code711_housekeeping/Resources/Public/Icons/Extension.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general, title, code,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden
            '
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
            'label' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:group.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'code' => [
            'label' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:group.code',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
    ],
];
