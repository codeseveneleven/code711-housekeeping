<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 project.
 * (c) 2022 B-Factor GmbH
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
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use JsonException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ApiService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getLatestTypo3Release(string $projectVersion): array
    {
        $major = substr($projectVersion, 0, strpos($projectVersion, '.'));
        if ($major == 6) {
            $major = substr($projectVersion, 0, 3);
        }
        $apiUrl = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('code711_housekeeping', 'typo3Url');
        return $this->getLatestTypo3ReleaseCall($apiUrl, $major);
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getLatestTypo3ReleaseCall(string $apiUrl, string $major)
    {
        $release = [];
        if ($apiUrl && $major) {
            $client = new Client();
            $res = $client->get($apiUrl . 'major/' . $major . '/release/latest');
            if ($res->getStatusCode() === 200 && $res->getHeader('content-type')[0] === 'application/json') {
                $release = json_decode($res->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            }
        }
        return $release;
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function projectVersion(array $project): string
    {
        $version = '';
        $apiUser = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['code711_housekeeping']['REST_API_USER'];
        $apiPw = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['code711_housekeeping']['REST_API_PW'];
        if (!empty($project['url'])) {
            $version = $this->projectVersionCall($project['url'], $apiUser, $apiPw);
            if ($version) {
                $this->logger->info('report ' . $project['url'] . 'api/v1/version', [$version]);
            } else {
                $this->logger->error('can not access ' . $project['url'] . 'api/v1/version');
            }
        }
        return $version;
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function projectVersionCall(string $url, string $apiUser, string $apiPw): string
    {
        if ($url && $apiUser && $apiPw) {
            $client = new Client();
            $res = $client->request('get', $url . 'api/v1/version', ['auth' => [$apiUser, $apiPw]]);
            if ($res->getStatusCode() === 200 && (
                    $res->getHeader('content-type')[0] === 'application/json'
                    || $res->getHeader('content-type')[0] === 'application/json; charset=utf-8'
                )) {
                $result = json_decode($res->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
                if (!empty($result['version'])) {
                    return $result['version'];
                }
            }
        }
        return '';
    }
}
