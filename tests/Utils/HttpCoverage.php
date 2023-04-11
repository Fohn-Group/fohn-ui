<?php

declare(strict_types=1);

// log coverage for test-suite

namespace Fohn\Ui\Tests\Utils;

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\Report;

final class HttpCoverage
{
    /** @var CodeCoverage */
    private static $coverage;

    private function __construct()
    {
        // zeroton
    }

    public static function start(): void
    {
        if (self::$coverage !== null) {
            throw new \Error('Coverage already started');
        }

        $filter = new Filter();
        $filter->includeDirectory(__DIR__ . '/../src');
        self::$coverage = new CodeCoverage(
            (new \SebastianBergmann\CodeCoverage\Driver\Selector())->forLineCoverage($filter),
            $filter
        );

        self::$coverage->start($_SERVER['SCRIPT_NAME']);
    }

    public static function saveData(): void
    {
        self::$coverage->stop();
        $filename = dirname(__DIR__, 2) . '/build/logs/' . basename($_SERVER['SCRIPT_NAME'] ?? 'unknown') . '-' . hash('sha256', microtime(true) . random_bytes(64)) . '.cov';
        (new Report\PHP())->process(self::$coverage, $filename);
        self::$coverage = null;
    }
}
