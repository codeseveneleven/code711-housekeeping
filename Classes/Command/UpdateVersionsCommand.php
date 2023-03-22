<?php

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

namespace Code711\Code711Housekeeping\Command;

use Code711\Code711Housekeeping\Service\UpdateService;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class UpdateVersionsCommand extends Command
{

    protected function configure(): void
    {
        $this->setDescription('Update used Versions in data records

        ./vendor/bin/typo3 housekeeping:update

        ');

        $this->addArgument(
            'group',
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'group identifier',
            ['sudhaus7', '12bis3', 'code711']
        );
    }

    /**
     * @throws Exception
     * @throws DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logger = GeneralUtility::makeInstance(ConsoleLogger::class, $output);

        /** @var QueryBuilder $query */
        $query = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_code711housekeeping_domain_model_project');
        $stmt = $query->select('*')
            ->from('tx_code711housekeeping_domain_model_project')
            ->where(
                $query->expr()->in('group', "'" . implode("','", $input->getArgument('group')) . "'")
            )
            ->execute();

        $updateService = GeneralUtility::makeInstance(UpdateService::class);
        $updateService->setLogger($logger);
        while ($row = $stmt->fetchAssociative()) {
            $output->writeln(sprintf('running %s (%s)', $row['title'], $row['url']));
            try {
                $major = substr($row['version'], 0, strpos($row['version'], '.'));
                if ($major >= 11) {
                    $row['version'] = null;
                }
                $updateService->updateProject($row['uid'], $row);
            } catch (\Exception $e) {
                $output->writeln('Error ' . $e->getMessage());
            }
        }
        return 0;
    }
}
