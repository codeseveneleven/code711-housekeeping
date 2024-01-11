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

use Code711\Code711Housekeeping\Domain\Model\Typo3Release;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class Typo3ApiService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected string $apiUrl = '';

    protected string $projectMajorVersion = '';

    public function __construct(string $apiUrl, string $projectMajorVersion)
    {
        $this->apiUrl = $apiUrl;
        $this->projectMajorVersion = $projectMajorVersion;
        if (empty($this->apiUrl)) {
            throw new \InvalidArgumentException('Config missing', 1677369373);
        }
    }

    /**
     * @throws JsonException
     */
    public function getLatestTypo3Release(): ?Typo3Release
    {
        $release = new Typo3Release();

        if ($this->apiUrl && $this->projectMajorVersion) {
            $client = new Client();
            try {
                $res = $client->get($this->apiUrl . 'major/' . $this->projectMajorVersion . '/release/latest');
            } catch (GuzzleException $e) {
                return null;
            }
            if ($res->getStatusCode() === 200 && $res->getHeader('content-type')[0] === 'application/json') {
                $result = json_decode($res->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

                $release->setType($result['type'] ?? '');
                $release->setElts($result['elts'] ?? false);
                $release->setVersion($result['version'] ?? '');
            } else {
                return null;
            }
        }
        return $release;
    }
}
