<?php

declare(strict_types=1);

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

namespace Code711\Code711Housekeeping\Service;

use Code711\Code711Housekeeping\Domain\Model\Release;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Typo3ApiService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected string $apiUrl = '';

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function __construct()
    {
        $this->apiUrl = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('code711_housekeeping', 'typo3Url');
        if (empty($this->apiUrl)) {
            throw new \InvalidArgumentException('Config missing', 1677369373);
        }
    }

    /**
     * @throws JsonException
     */
    public function getLatestTypo3Release(string $projectVersion): bool|Release
    {
        $major = substr($projectVersion, 0, strpos($projectVersion, '.'));
        if ($major == 6) {
            $major = substr($projectVersion, 0, 3);
        }
        return $this->getLatestTypo3ReleaseCall($this->apiUrl, $major);
    }

    /**
     * @throws JsonException
     */
    public function getLatestTypo3ReleaseCall(string $apiUrl, string $major): bool|Release
    {
        $release = new Release();

        if ($apiUrl && $major) {
            $client = new Client();
            try {
                $res = $client->get($apiUrl . 'major/' . $major . '/release/latest');
            } catch (GuzzleException $e) {
                return false;
            }
            if ($res->getStatusCode() === 200 && $res->getHeader('content-type')[0] === 'application/json') {
                $result = json_decode($res->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

                $release->setType($result['type'] ?? '');
                $release->setElts($result['elts'] ?? false);
                $release->setVersion($result['version'] ?? '');
            } else {
                return false;
            }
        }
        return $release;
    }
}
