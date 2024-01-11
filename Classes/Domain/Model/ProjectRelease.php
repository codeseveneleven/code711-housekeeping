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

namespace Code711\Code711Housekeeping\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class ProjectRelease
{
    protected string $version = '';

    protected string $php = '';

    /**
     * @var ObjectStorage<Package>
     */
    protected ObjectStorage $packages;

    public function __construct()
    {
        $this->packages = new ObjectStorage();
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getPhp(): string
    {
        return $this->php;
    }

    public function setPhp(string $php): void
    {
        $this->php = $php;
    }

    public function getPackages(): ?ObjectStorage
    {
        return $this->packages;
    }

    public function setPackages(?ObjectStorage $packages): void
    {
        $this->packages = $packages;
    }

    /**
     * @param Package $package
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
