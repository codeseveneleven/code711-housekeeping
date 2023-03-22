<?php

/*
 * This file is part of the TYPO3 project.
 * (c) 2022 12bis3
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 * The TYPO3 project - inspiring people to share!
 * @copyright 2022 12bis3 https://12bis3.de/
 *
 */

if (! defined('TYPO3')) {
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \Code711\Code711Housekeeping\Hooks\DatamapPostProcess::class;
