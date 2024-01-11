<?php

declare(strict_types=1);

namespace Code711\Code711Housekeeping\Test\Functional\Service;

use Code711\Code711Housekeeping\Service\Typo3ApiService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class Typo3ApiServiceTest extends UnitTestCase
{
    /**
     * @test
     */
    public function checkGetLatestTypo3Release(): void
    {
        $typo3ApiService = new Typo3ApiService();
    }
}