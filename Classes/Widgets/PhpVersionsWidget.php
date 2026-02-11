<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 project.
 * (c) 2026 B-Factor GmbH
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

use Doctrine\DBAL\Exception;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;

class PhpVersionsWidget implements WidgetInterface, RequestAwareWidgetInterface, AdditionalCssInterface
{
    private ServerRequestInterface $request;

    public function __construct(
        private readonly WidgetConfigurationInterface $configuration,
        private readonly BackendViewFactory $backendViewFactory,
        private array $options = []
    ) {}

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * @throws Exception
     */
    public function renderWidgetContent(): string
    {
        $view = $this->backendViewFactory->create($this->request);
        $view->assignMultiple([
            'items' => $this->getItems(),
            'configuration' => $this->configuration,
        ]);
        return $view->render('Widget/PhpVersionsWidget');
    }

    /**
     * @throws Exception
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
                        Connection::PARAM_STR_ARRAY
                    )
                )
            );
        }

        if (!empty($this->options['sorting'])) {
            foreach ($this->options['sorting'] as $sorting) {
                $explodes = explode(' ', (string)$sorting);
                if (isset($explodes[0]) && ($explodes[0] !== '' && $explodes[0] !== '0')) {
                    $order = $explodes[1] ?? 'ASC';
                    $queryBuilder->addOrderBy($explodes[0], $order);
                }
            }
        }

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    public function getCssFiles(): array
    {
        return [];
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
