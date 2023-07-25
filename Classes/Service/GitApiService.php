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

use Code711\Code711Housekeeping\Domain\Model\Project;
use Gitlab\Client;
use RuntimeException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GitApiService
{
    protected string $giturl = '';

    protected string $gitToken = '';

    /**
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     */
    public function __construct()
    {
        $this->gitToken = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('code711_housekeeping', 'http_auth_token');
        if (!$this->gitToken) {
            throw new \InvalidArgumentException('Config missing', 1677369373);
        }
    }

    public function getProjectRelease(Project $project): Project
    {
        $this->giturl = $project->getGiturl();
        if ($project->getGittoken()) {
            $this->gitToken = $project->getGittoken();
        }

        $file = $this->readComposerLock();
        if ($file) {
            $packages = $file->packages;
            foreach ($packages as $package) {
                if ($package->name === 'typo3/cms-core') {
                    $project->setVersion(trim($package->version, 'v'));
                }
            }
        }

        return $project;
    }

    public function readComposerLock()
    {
        $client = $this->connect();
        try {
            $file = $client->repositoryFiles()->getRawFile($this->getProject(), 'composer.lock', 'master');
            return json_decode($file);
        } catch (RuntimeException $e) {
            return false;
        }
    }

    public function connect(): Client
    {
        $client = new Client();
        $client->setUrl($this->getHost());
        $client->authenticate($this->gitToken, Client::AUTH_HTTP_TOKEN);
        return $client;
    }

    public function getHost(): string
    {
        $uri = \parse_url($this->giturl);
        if (isset($uri['host']) && isset($uri['scheme'])) {
            return $uri['scheme'] . '://' . $uri['host'] . '/';
        }
        return $this->giturl;
    }

    public function getProject(): string
    {
        return trim((string)\parse_url($this->giturl, PHP_URL_PATH), '/');
    }

}