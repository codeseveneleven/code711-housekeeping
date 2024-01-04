<?php

declare(strict_types=1);

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

namespace Code711\Code711Housekeeping\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PackagistApiService
{
    protected string $packagistUrl = '';

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function __construct()
    {
        $this->packagistUrl = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('code711_housekeeping', 'packagistUrl');
        if (empty($this->packagistUrl)) {
            throw new \InvalidArgumentException('Config missing', 1677369373);
        }
    }

    public function getPackageVersion(string $package): bool|string
    {
        $client = new Client();
        try {
            $res = $client->get($this->packagistUrl . $package . '.json');
        } catch (GuzzleException $e) {
            return false;
        }
        if ($res->getStatusCode() === 200 && $res->getHeader('content-type')[0] === 'application/json') {
            $result = json_decode($res->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            return $result['packages'][$package][0]['version'];
        }
            return false;
    }
}
