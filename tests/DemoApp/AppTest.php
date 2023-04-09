<?php

declare(strict_types=1);
/**
 * Test app-test folder.
 */

namespace Fohn\Ui\Tests\DemoApp;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    protected const ROOT_DIR = __DIR__ . '/../..';
    protected const APP_DIR = self::ROOT_DIR . '/app-test';

    protected Client $client;

    protected function setUp(): void
    {
        require_once __DIR__ . '/../init-configuration.php';

        $this->client = new Client(
            [
                'base_uri' => loadConfig()['base_uri'],
                'headers' => ['x-coverage-id' => 'pcov'],
            ]
        );
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
    public function testAppTestHtmlResponse(string $uri): void
    {
        $request = new Request('GET', '/app-test/' . $uri);
        $response = $this->client->send($request);

        $this->assertSame(200, $response->getStatusCode(), ' Status error on ' . $uri);
    }
}
