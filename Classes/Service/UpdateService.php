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

use Code711\Code711Housekeeping\Domain\Repository\ProjectRepository;
use Doctrine\DBAL\Statement;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class UpdateService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected ProjectRepository $projectRepository;

    public function injectProjectRepository(ProjectRepository $projectRepository): void
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function updateProject(int $id, array $record): void
    {
        if ($id && !empty($record['url'])) {
            $typo3VersionChecker = GeneralUtility::makeInstance(ApiService::class);
            $typo3VersionChecker->setLogger($this->logger);

            $projectVersion = $record['version'];
            try {
                $checkedVersion = $typo3VersionChecker->projectVersion($record);
                if ($checkedVersion) {
                    $projectVersion = $checkedVersion;
                }
            } catch (GuzzleException|\JsonException $e) {
                $this->logger->error($e->getCode() . ': ' . $e->getMessage());
            }

            if ($projectVersion) {
                $latestRelease = '';
                try {
                    $latestRelease = $typo3VersionChecker->getLatestTypo3Release($projectVersion);
                    $this->logger->info('fetching latest release');
                } catch (GuzzleException|\JsonException $e) {
                    $this->logger->error($e->getCode() . ': ' . $e->getMessage());
                }

                if (!empty($latestRelease['version']) && !empty($latestRelease['type'])) {
                    $settings = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('code711_housekeeping');
                    $severity = $this->checkSeverity($projectVersion, $latestRelease['version'], $latestRelease['type'], $settings);

                    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_code711housekeeping_domain_model_project');
                    /** @var Statement $stmt */
                    $queryBuilder
                        ->update('tx_code711housekeeping_domain_model_project')
                        ->set('latest', $latestRelease['version'])
                        ->set('version', $projectVersion)
                        ->set('type', $latestRelease['type'])
                        ->set('elts', (int)$latestRelease['elts'])
                        ->set('severity', $severity)->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($id, Connection::PARAM_INT)))->executeStatement();
                }
            }
        }
    }

    public function checkSeverity(string $projectVersion, string $latestVersion, string $latestType, array $settings): string
    {
        $major = substr($projectVersion, 0, strpos($projectVersion, '.'));
        if (in_array($major, GeneralUtility::trimExplode(',', $settings['redVersions']))) {
            return 'bg-red';
        }
        if (in_array($major, GeneralUtility::trimExplode(',', $settings['orangeVersions']))) {
            return 'bg-orange';
        }
        if ($projectVersion === $latestVersion) {
            return 'bg-green';
        }
        if ($latestType === 'security') {
            return 'bg-xdarkred';
        }
        return 'bg-orange';
    }
}
