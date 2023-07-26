<?php

/*
 * This file is part of the TYPO3 project.
 * (c) 2023 B-Factor GmbH
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
        'title'	=> 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project',
        'label' => 'title',
        'label_alt' => 'url,version,latest',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
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
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general, title, group, giturl, gittoken, gitbranch, url,
                --div--;LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.tab.project, version, php, severity,
                --div--;LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.tab.typo3, latest, type, elts,
                --div--;LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.tab.packages, packages,
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
            'label' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'url' => [
            'label' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.url',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'version' => [
            'label' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.version',
            'description' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.version.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'latest' => [
            'label' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.latest',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'type' => [
            'label' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.type',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'elts' => [
            'label' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.elts',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'severity' => [
            'label' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.severity',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['Not checked', ''],
                    ['Green', 'bg-green'],
                    ['Orange', 'bg-orange'],
                    ['Red', 'bg-red'],
                    ['Dark red', 'bg-xdarkred'],
                ],
                'default' => '',
            ],
        ],
        'group' => [
            'label' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.group',
            'description' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.group.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_code711housekeeping_domain_model_group',
                'default' => 0,
            ],
        ],
        'giturl' => [
            'label' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.giturl',
            'description' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.giturl.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'gittoken' => [
            'label' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.gittoken',
            'description' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.gittoken.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'gitbranch' => [
            'label' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.gitbranch',
            'description' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.gitbranch.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'php' => [
            'label' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.php',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'packages' => [
            'label' => 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:project.packages',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_code711housekeeping_domain_model_package',
                'foreign_field' => 'parentid',
                'foreign_table_field' => 'parenttable',
                'appearance' => [
                    'showSynchronizationLink' => true,
                    'showAllLocalizationLink' => true,
                    'showPossibleLocalizationRecords' => true,
                ],
                'default' => 0,
            ],
        ],
    ],
];
