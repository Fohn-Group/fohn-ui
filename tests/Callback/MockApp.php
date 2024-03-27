<?php

declare(strict_types=1);
/**
 * App for Callback testing.
 */

namespace Fohn\Ui\Tests\Callback;

class MockApp extends \Fohn\Ui\App
{
    protected bool $registerShutdown = false;

    public function terminateJson(array $output, int $statusCode = 200): void
    {
    }

    public function callExit(): void
    {
    }

    public function streamEvent(array $event, int $bufferSize = 0): void
    {
    }
}
