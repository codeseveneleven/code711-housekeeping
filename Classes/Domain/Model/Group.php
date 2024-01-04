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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Group extends AbstractEntity
{
    protected string $title = '';

    protected string $gittoken = '';

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
}
