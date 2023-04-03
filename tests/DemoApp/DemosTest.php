<?php

declare(strict_types=1);

namespace Fohn\Ui\Tests\DemoApp;

use Fohn\Ui\Tests\TestCase\HttpTestCase;

/**
 * Test if all demos can be rendered successfully.
 *
 * Requests are emulated in the same process.
 */
class DemosTest extends HttpTestCase
{
    protected const ROOT_DIR = __DIR__ . '/../..';
    protected const APP_DIR = self::ROOT_DIR . '/app-test';

    protected function setUp(): void
    {
        require_once __DIR__ . '/init-test-ui.php';
    }

    public function demoFilesProvider(): array
    {
        $excludeDirs = ['_app-data', '_includes', 'local'];
        $excludeFiles = [];

        $files = [];
        $files[] = 'index.php';
        foreach (array_diff(scandir(static::APP_DIR), ['.', '..'], $excludeDirs) as $dir) {
            if (!is_dir(static::APP_DIR . '/' . $dir)) {
                continue;
            }

            foreach (scandir(static::APP_DIR . '/' . $dir) as $f) {
                $path = $dir . '/' . $f;
                if (substr($path, -4) !== '.php' || in_array($path, $excludeFiles, true)) {
                    continue;
                }

                $files[] = $path;
            }
        }

        return array_reduce($files, function (array $items, string $v) {
            $items[$v] = [$v];

            return $items;
        }, []);
    }

    /**
     * @dataProvider demoFilesProvider
     */
    public function testDemosStatusAndHtmlResponse(string $uri): void
    {
        if ($uri === 'form/form-model-controller.php') {
            $t = 't';
        }
        $response = $this->getResponseFromRequest($uri);
        $this->assertSame(200, $response->getStatusCode(), ' Status error on ' . $uri);
    }
}
