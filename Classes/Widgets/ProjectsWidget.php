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

namespace Code711\Code711Housekeeping\Widgets;

use Doctrine\DBAL\Connection as ConnectionAlias;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ProjectsWidget implements WidgetInterface, AdditionalCssInterface
{
    public function __construct(
        private WidgetConfigurationInterface $configuration,
        protected ?StandaloneView $view = null,
        private array $options = []
    ) {
        if (VersionNumberUtility::getNumericTypo3Version() < 12) {
            $this->setOptions($this->options);
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function renderWidgetContent(): string
    {
        $this->view->assignMultiple([
            'items' => $this->getItems(),
            'configuration' => $this->configuration,
        ]);
        return $this->view->render('ProjectsWidget');
    }

    /**
     * @return array
     * @throws DBALException
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function getItems(): array
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('tx_code711housekeeping_domain_model_project');
        $queryBuilder
            ->select('p.*')
            ->from('tx_code711housekeeping_domain_model_project', 'p')
            ->join('p', 'tx_code711housekeeping_domain_model_group', 'g', 'p.group = g.uid');

        if (!empty($this->options['groups'])) {
            $queryBuilder->where(
                $queryBuilder->expr()->in(
                    'g.code',
                    $queryBuilder->createNamedParameter(
                        $this->options['groups'],
                        ConnectionAlias::PARAM_STR_ARRAY
                    )
                )
            );
        }

        if (!empty($this->options['sorting'])) {
            foreach ($this->options['sorting'] as $sorting) {
                $explodes = explode(' ', $sorting);
                if (!empty($explodes[0])) {
                    $order = $explodes[1] ?? 'ASC';
                    $queryBuilder->addOrderBy($explodes[0], $order);
                }
            }
        }

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    public function getCssFiles(): array
    {
        return [
            'EXT:code711_housekeeping/Resources/Public/Css/ProjectWidget.css',
        ];
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
