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

namespace Code711\Code711Housekeeping\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Project extends AbstractEntity
{
    protected string $title = '';

    protected string $url = '';

    protected string $version = '';

    protected string $latest = '';

    protected string $type = '';

    protected bool $elts = false;

    protected string $severity = '';

    protected ?Group $group = null;

    protected string $giturl = '';

    protected string $gittoken = '';

    protected string $gitbranch = '';

    protected string $php = '';

    /**
     * @var ObjectStorage<Package>|null
     */
    protected ?ObjectStorage $packages = null;

    public function __construct()
    {
        $this->packages = new ObjectStorage();
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getLatest(): string
    {
        return $this->latest;
    }

    /**
     * @param string $latest
     */
    public function setLatest(string $latest): void
    {
        $this->latest = $latest;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isElts(): bool
    {
        return $this->elts;
    }

    /**
     * @param bool $elts
     */
    public function setElts(bool $elts): void
    {
        $this->elts = $elts;
    }

    /**
     * @return string
     */
    public function getSeverity(): string
    {
        return $this->severity;
    }

    /**
     * @param string $severity
     */
    public function setSeverity(string $severity): void
    {
        $this->severity = $severity;
    }

    /**
     * @return Group|null
     */
    public function getGroup(): ?Group
    {
        return $this->group;
    }

    /**
     * @param Group|null $group
     */
    public function setGroup(?Group $group): void
    {
        $this->group = $group;
    }

    /**
     * @return string
     */
    public function getGiturl(): string
    {
        return $this->giturl;
    }

    /**
     * @param string $giturl
     */
    public function setGiturl(string $giturl): void
    {
        $this->giturl = $giturl;
    }

    /**
     * @return string
     */
    public function getGittoken(): string
    {
        return $this->gittoken;
    }

    /**
     * @param string $gittoken
     */
    public function setGittoken(string $gittoken): void
    {
        $this->gittoken = $gittoken;
    }

    /**
     * @return string
     */
    public function getGitbranch(): string
    {
        return $this->gitbranch;
    }

    /**
     * @param string $gitbranch
     */
    public function setGitbranch(string $gitbranch): void
    {
        $this->gitbranch = $gitbranch;
    }

    /**
     * @return string
     */
    public function getPhp(): string
    {
        return $this->php;
    }

    /**
     * @param string $php
     */
    public function setPhp(string $php): void
    {
        $this->php = $php;
    }

    /**
     * @return ObjectStorage|null
     */
    public function getPackages(): ?ObjectStorage
    {
        return $this->packages;
    }

    /**
     * @param ObjectStorage|null $packages
     */
    public function setPackages(?ObjectStorage $packages): void
    {
        $this->packages = $packages;
    }

    /**
     * @param Package $package
     * @return void
     */
    public function addPackage(Package $package): void
    {
        $this->packages->attach($package);
    }

    public function hasPackage(string $name): bool
    {
        $packages = $this->packages;
        foreach ($packages as $package) {
            if ($package->getTitle() === $name) {
                return true;
            }
        }
        return false;
    }
}
