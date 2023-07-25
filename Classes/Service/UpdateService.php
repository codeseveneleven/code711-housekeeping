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
use Code711\Code711Housekeeping\Domain\Model\Release;
use Code711\Code711Housekeeping\Domain\Repository\ProjectRepository;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class UpdateService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected ?ProjectRepository $projectRepository = null;

    protected string $orangeVersions = '';

    protected string $redVersions = '';

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function __construct()
    {
        $this->orangeVersions = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('code711_housekeeping', 'orangeVersions');
        $this->redVersions = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('code711_housekeeping', 'redVersions');
        $this->projectRepository = GeneralUtility::makeInstance(ProjectRepository::class);

        if (empty($this->orangeVersions) || empty($this->redVersions)) {
            throw new \InvalidArgumentException('Config missing', 1677369373);
        }
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function updateProject(int $id): void
    {
        $project = $this->projectRepository->findByUid($id);

        if ($project instanceof Project) {

            if ($project->getGiturl()) {
                $this->logger->info('fetching latest project release');
                $gitApiService = GeneralUtility::makeInstance(GitApiService::class);
                $project = $gitApiService->getProjectRelease($project);
            }

            $this->logger->info('fetching latest typo3 release');
            $typo3ApiService = GeneralUtility::makeInstance(Typo3ApiService::class);
            $latestRelease = $typo3ApiService->getLatestTypo3Release($project->getVersion());

            if ($latestRelease) {
                $project->setLatest($latestRelease->getVersion());
                $project->setElts($latestRelease->isElts());
                $project->setType($latestRelease->getType());
                $severity = $this->checkSeverity($project->getVersion(), $latestRelease);
                $project->setSeverity($severity);
            }

            $this->projectRepository->update($project);
            $persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
            $persistenceManager->persistAll();

        }
    }

    public function checkSeverity(string $projectVersion, Release $latestVersion): string
    {
        $major = substr($projectVersion, 0, strpos($projectVersion, '.'));

        if (in_array($major, GeneralUtility::trimExplode(',', $this->redVersions))) {
            return 'bg-red';
        }
        if (in_array($major, GeneralUtility::trimExplode(',', $this->orangeVersions))) {
            return 'bg-orange';
        }
        if ($projectVersion === $latestVersion->getVersion()) {
            return 'bg-green';
        }
        if ($latestVersion->getType() === 'security') {
            return 'bg-xdarkred';
        }
        return 'bg-orange';
    }
}
