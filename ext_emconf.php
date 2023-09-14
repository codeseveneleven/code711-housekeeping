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

$EM_CONF[$_EXTKEY] = [
    'title' => 'Code711 Housekeeping',
    'description' => 'Keep track of your TYPO3 versions by checking your git-repositories (gitlab for now). You can then see the results on your TYPO3 Dashboard. It is of course checked against the currently available latest TYPO3 version, keeping you updated on what needs to be updated.',
    'category' => 'plugin',
    'author' => 'Patricia Ottmar',
    'author_email' => 'p.ottmar@12bis3.de',
    'author_company' => '12bis3',
    'state' => 'beta',
    'version' => '3.3.0',
    'constraints' => [
        'depends' => [],
        'conflicts' => [],
        'suggests' => [],
    ],
];
