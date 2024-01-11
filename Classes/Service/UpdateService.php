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
use Code711\Code711Housekeeping\Domain\Model\Typo3Release;
use Code711\Code711Housekeeping\Domain\Repository\ProjectRepository;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class UpdateService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected ProjectRepository $projectRepository;

    protected PersistenceManager $persistenceManager;

    protected ExtensionConfiguration $extensionConfiguration;

    protected string $gitToken = '';

    protected string $defaultBranch = '';

    protected string $orangeVersions = '';

    protected string $redVersions = '';

    protected string $apiUrl = '';

    protected string $packagistUrl = '';

    public function injectProjectRepository(ProjectRepository $projectRepository): void
    {
        $this->projectRepository = $projectRepository;
    }

    public function injectPersistenceManager(PersistenceManager $persistenceManager): void
    {
        $this->persistenceManager = $persistenceManager;
    }

    public function injectExtensionConfiguration(ExtensionConfiguration $extensionConfiguration): void
    {
        $this->extensionConfiguration = $extensionConfiguration;
    }

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function __construct()
    {
        $this->gitToken = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('code711_housekeeping', 'http_auth_token');
        $this->defaultBranch = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('code711_housekeeping', 'defaultBranch');

        $this->orangeVersions = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('code711_housekeeping', 'orangeVersions');
        $this->redVersions = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('code711_housekeeping', 'redVersions');

        $this->apiUrl = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('code711_housekeeping', 'typo3Url');
        $this->packagistUrl = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('code711_housekeeping', 'packagistUrl');

        if (empty($this->orangeVersions) || empty($this->redVersions)) {
            throw new \InvalidArgumentException('Config missing', 1677369373);
        }
    }

    /**
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     * @throws \JsonException
     */
    public function updateProject(int $id): void
    {
        $project = $this->projectRepository->findByUid($id);

        if ($project instanceof Project) {
            if ($project->getGiturl()) {
                $this->logger->info('fetching latest project release');

                if ($project->getGroup()->getGittoken()) {
                    $this->gitToken = $project->getGroup()->getGittoken();
                } else if ($project->getGittoken()) {
                    $this->gitToken = $project->getGittoken();
                }
                if ($project->getGitbranch()) {
                    $this->defaultBranch = $project->getGitbranch();
                }

                $gitApiService = new GitApiService($this->gitToken, $this->defaultBranch, $project->getGiturl());
                $projectRelease = $gitApiService->getProjectRelease();

                $project->setVersion($projectRelease->getVersion());
                $project->setPhp($projectRelease->getPhp());
                $project->setPackages($projectRelease->getPackages());

                $this->logger->info('fetching latest package releases');

                /** @var Package $package */
                foreach ($project->getPackages() as $package) {
                    $packagistApiService = new PackagistApiService($this->packagistUrl);
                    $packageLatest = $packagistApiService->getPackageVersion($package->getTitle());
                    if ($packageLatest) {
                        $package->setLatest($packageLatest);
                    }
                }
            }

            $this->logger->info('fetching latest typo3 release');

            $projectMajorVersion = substr($project->getVersion(), 0, strpos($project->getVersion(), '.'));
            if ($projectMajorVersion == 6) {
                $projectMajorVersion = substr($project->getVersion(), 0, 3);
            }

            $typo3ApiService = new Typo3ApiService($this->apiUrl, $projectMajorVersion);
            $typo3Release = $typo3ApiService->getLatestTypo3Release();

            if ($typo3Release) {
                $project->setLatest($typo3Release->getVersion());
                $project->setElts($typo3Release->isElts());
                $project->setType($typo3Release->getType());
                $severity = $this->checkSeverity($project->getVersion(), $typo3Release);
                $project->setSeverity($severity);
            }

            $this->projectRepository->update($project);
            $this->persistenceManager->persistAll();
        }
    }

    public function checkSeverity(string $projectVersion, Typo3Release $latestVersion): string
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
