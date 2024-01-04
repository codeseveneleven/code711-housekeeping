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

use Code711\Code711Housekeeping\Domain\Model\Package;
use Code711\Code711Housekeeping\Domain\Model\Project;
use Gitlab\Client;
use JsonException;
use RuntimeException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GitApiService
{
    protected string $giturl = '';

    protected string $gitToken = '';

    protected string $defaultBranch = '';

    /**
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     */
    public function __construct()
    {
        $this->gitToken = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('code711_housekeeping', 'http_auth_token');
        $this->defaultBranch = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('code711_housekeeping', 'defaultBranch');

        if (!$this->gitToken) {
            throw new \InvalidArgumentException('Config missing', 1677369373);
        }
    }

    /**
     * @throws JsonException
     */
    public function getProjectRelease(Project $project): Project
    {
        $this->giturl = $project->getGiturl();
        if ($project->getGroup()->getGittoken()) {
            $this->gitToken = $project->getGroup()->getGittoken();
        }
        if ($project->getGittoken()) {
            $this->gitToken = $project->getGittoken();
        }
        if ($project->getGitbranch()) {
            $this->defaultBranch = $project->getGitbranch();
        }

        $file = $this->readComposerLock();
        if ($file) {
            foreach ($file as $key => $value) {
                if ($key === 'platform-overrides') {
                    $project->setPhp($value->php);
                }
            }
            foreach ($file->packages as $item) {
                if ($item->name === 'typo3/cms-core') {
                    $project->setVersion(trim($item->version, 'v'));
                }
                if ($item->type === 'typo3-cms-extension' && !$project->hasPackage($item->name)) {
                    $package = new Package();
                    $package->setTitle($item->name);
                    $package->setVersion($item->version);

                    $packagistApiService = GeneralUtility::makeInstance(PackagistApiService::class);
                    $packageLatest = $packagistApiService->getPackageVersion($item->name);
                    if ($packageLatest) {
                        $package->setLatest($packageLatest);
                    }

                    $project->addPackage($package);
                }
            }
        }

        return $project;
    }

    public function readComposerLock()
    {
        $client = $this->connect();
        try {
            $file = $client->repositoryFiles()->getRawFile($this->getProject(), 'composer.lock', $this->defaultBranch);
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
