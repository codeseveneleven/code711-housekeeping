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

namespace Code711\Code711Housekeeping\Test\Unit\Service;

use Code711\Code711Housekeeping\Service\ApiService;
use TYPO3\TestingFramework\Core\BaseTestCase;

class ApiServiceTest extends BaseTestCase
{
    /**
     * @test
     *
     * @dataProvider getLatestTypo3ReleaseCallDataProvider
     */
    public function getLatestTypo3ReleaseCall(string $expected, string $given): void
    {
        $typo3VersionChecker = new ApiService();
        $result = $typo3VersionChecker->getLatestTypo3ReleaseCall('https://get.typo3.org/v1/api/', $given);
        self::assertMatchesRegularExpression($expected, $result['version'] ?? '');
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function getLatestTypo3ReleaseCallDataProvider(): array
    {
        return [
            'releaseIs60' => [
                '/6\\.0\\.[0-9]+/i', '6.0',
            ],
            'releaseIs61' => [
                '/6\\.1\\.[0-9]+/i', '6.1',
            ],
            'releaseIs7' => [
                '/7\\.[0-9]+\\.[0-9]+/i', '7',
            ],
            'releaseIs8' => [
                '/8\\.[0-9]+\\.[0-9]+/i', '8',
            ],
            'releaseIs9' => [
                '/9\\.[0-9]+\\.[0-9]+/i', '9',
            ],
            'releaseIs10' => [
                '/10\\.[0-9]+\\.[0-9]+/i', '10',
            ],
            'releaseIs11' => [
                '/11\\.[0-9]+\\.[0-9]+/i', '11',
            ],
            'releaseIs12' => [
                '/12\\.[0-9]+\\.[0-9]+/i', '12',
            ],
            'noInputGiven' => [
                '//i', '',
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider projectVersionCallDataProvider
     */
    public function projectVersionCall(string $expected, string $given): void
    {
        $typo3VersionChecker = new ApiService();
        $result = $typo3VersionChecker->projectVersionCall($given, 'BasicSecured', '3QT{7b~?5l10)oCK}NNx');
        if ($result) {
            self::assertMatchesRegularExpression($expected, $result);
        } else {
            self::assertEmpty($result);
        }
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function projectVersionCallDataProvider(): array
    {
        return [
            'testcode711page' => [
                '/11\\.5\\.[0-9]+/i', 'https://code711.de/',
            ],
            'noInputGiven' => [
                '', '',
            ],
        ];
    }
}
