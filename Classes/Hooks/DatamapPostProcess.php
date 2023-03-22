<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 project.
 * (c) 2022 B-Factor GmbH
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

namespace Code711\Code711Housekeeping\Hooks;

use Code711\Code711Housekeeping\Domain\Repository\ProjectRepository;
use Code711\Code711Housekeeping\Service\UpdateService;
use Doctrine\DBAL\DBALException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Exception;

class DatamapPostProcess implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected ProjectRepository $projectRepository;

    public function injectProjectRepository(ProjectRepository $projectRepository): void
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * @throws Exception
     */
    public function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, $pObj): void
    {
        if ($table == 'tx_code711housekeeping_domain_model_project' && ($status == 'new' || $status == 'update')) {
            $newid = $id;
            if ($status == 'new') {
                $newid = $pObj->substNEWwithIDs[$id];
            }
            $updateService = GeneralUtility::makeInstance(UpdateService::class);
            $updateService->setLogger($this->logger);
            try {
                $updateService->updateProject((int)$newid, $pObj->datamap[$table][$id]);
            } catch (GuzzleException|DBALException $e) {
                $flashMessage = GeneralUtility::makeInstance(
                    FlashMessage::class,
                    $e->getMessage(),
                    (string)$e->getCode(),
                    AbstractMessage::ERROR,
                    true
                );
                $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
                $defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
                $defaultFlashMessageQueue->enqueue($flashMessage);
            }
        }
    }
}
