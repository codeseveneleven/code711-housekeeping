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

class Package extends AbstractEntity
{
    protected string $title = '';

    protected string $version = '';

    protected string $latest = '';

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
}
