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
use Code711\Code711Housekeeping\Domain\Model\ProjectRelease;
use Gitlab\Client;
use JsonException;
use RuntimeException;

class GitApiService
{
    protected string $giturl = '';

    protected string $gitToken = '';

    protected string $defaultBranch = '';

    public function __construct(string $gitToken, string $defaultBranch, string $gitUrl)
    {
        $this->gitToken = $gitToken;
        $this->defaultBranch = $defaultBranch;
        $this->giturl = $gitUrl;
        if (!$this->gitToken) {
            throw new \InvalidArgumentException('Config missing', 1677369373);
        }
    }

    /**
     * @throws JsonException
     */
    public function getProjectRelease(): ProjectRelease
    {
        $projectRelease =  new ProjectRelease();

        $file = $this->readComposerLock();
        if ($file) {
            foreach ($file as $key => $value) {
                if ($key === 'platform-overrides') {
                    $projectRelease->setPhp($value->php);
                }
            }
            foreach ($file->packages as $item) {
                if ($item->name === 'typo3/cms-core') {
                    $projectRelease->setVersion(trim($item->version, 'v'));
                }
                if ($item->type === 'typo3-cms-extension' && !$projectRelease->hasPackage($item->name)) {
                    $package = new Package();
                    $package->setTitle($item->name);
                    $package->setVersion($item->version);
                    $projectRelease->addPackage($package);
                }
            }
        }

        return $projectRelease;
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
