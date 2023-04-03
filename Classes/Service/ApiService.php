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
     * @return array
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getLatestTypo3Releases(): array
    {
        // https://get.typo3.org/api/doc

        $apiUrl = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('code711_housekeeping', 'typo3Url');
        $minVersion = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('code711_housekeeping', 'minVersion');

        $releases = [];

        $client = new Client();
        $res = $client->get($apiUrl . 'major');
        if ($res->getStatusCode() === 200 && $res->getHeader('content-type')[0] === 'application/json') {
            $majors = json_decode($res->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            foreach ($majors as $major) {
                $version = $major['version'];
                if ($version >= $minVersion) {
                    $res = $client->get($apiUrl . 'major/' . $version . '/release/latest');
                    if ($res->getStatusCode() === 200 && $res->getHeader('content-type')[0] === 'application/json') {
                        $release = json_decode($res->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
                        $releases[$version] = $release;
                    }
                }
            }
        }

        return $releases;
    }

    /**
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getLatestTypo3Release(int $version): array
    {
        $release = [];

        $apiUrl = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('code711_housekeeping', 'typo3Url');

        $client = new Client();
        $res = $client->get($apiUrl . 'major/' . $version . '/release/latest');
        if ($res->getStatusCode() === 200 && $res->getHeader('content-type')[0] === 'application/json') {
            $release = json_decode($res->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        }

        return $release;
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function projectVersion(array $project): string
    {
        $apiUser = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['code711_housekeeping']['REST_API_USER'];
        $apiPw = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['code711_housekeeping']['REST_API_PW'];

        $client = new Client();
        $res = $client->request('get', $project['url'] . 'api/v1/version', ['auth' => [$apiUser, $apiPw]]);

        if ($res->getStatusCode() === 200 && (
                $res->getHeader('content-type')[0] === 'application/json'
                || $res->getHeader('content-type')[0] === 'application/json; charset=utf-8'
            )) {
            $result = json_decode($res->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            $this->logger->info('report ' . $project['url'] . 'api/v1/version', $result);
            if (!empty($result['version'])) {
                return $result['version'];
            }
        } else {
            $this->logger->error('can not access ' . $project['url'] . 'api/v1/version');
        }
        return '';
    }
}
