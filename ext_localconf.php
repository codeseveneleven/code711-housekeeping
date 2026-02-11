<?php

/*
 * This file is part of the TYPO3 project.
 * (c) 2026 B-Factor GmbH
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

use Code711\Code711Housekeeping\Hooks\DatamapPostProcess;

if (! defined('TYPO3')) {
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = DatamapPostProcess::class;
