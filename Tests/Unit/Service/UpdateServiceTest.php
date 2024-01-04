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

namespace Code711\Code711Housekeeping\Test\Unit\Service;

use Code711\Code711Housekeeping\Service\UpdateService;
use TYPO3\TestingFramework\Core\BaseTestCase;

class UpdateServiceTest extends BaseTestCase
{
    /**
     * @test
     *
     * @dataProvider checkSeverityDataProvider
     */
    public function checkSeverity(string $expected, array $given)
    {
        $settings = ['redVersions' => '6,7,8,9', 'orangeVersions' => '10'];
        $updateService = new UpdateService();
        $result = $updateService->checkSeverity($given[0], $given[1], $given[2], $settings);
        self::assertEquals($expected, $result);
    }

    public static function checkSeverityDataProvider(): array
    {
        return [
            'redsimple' => [
                'bg-red', ['8.7.32', '8.7.32', 'regular'],
            ],
            'orangesimple' => [
                'bg-orange', ['10.4.36', '10.4.36', 'regular'],
            ],
            'greensimple' => [
                'bg-green', ['11.5.25', '11.5.25', 'regular'],
            ],
            'darkred' => [
                'bg-xdarkred', ['11.5.24', '11.5.25', 'security'],
            ],
            'test61' => [
                'bg-red', ['6.1.12', '6.1.12', 'regular'],
            ],
            'test62' => [
                'bg-red', ['6.2.31', '6.2.31', 'regular'],
            ],
            'test122' => [
                'bg-orange', ['12.2.0', '12.3.0', 'regular'],
            ],
            'test123' => [
                'bg-green', ['12.3.0', '12.3.0', 'regular'],
            ],
        ];
    }
}
