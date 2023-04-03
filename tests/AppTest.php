<?php

declare(strict_types=1);

namespace Fohn\Ui\Tests;

use Fohn\Ui\App;
use Fohn\Ui\Service\Ui;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    protected function getApp()
    {
        return new App(['registerShutdown' => false, 'callExit' => false]);
    }

    public function testAppOutputHandler(): void
    {
        $app = $this->getApp();
        $app->setOutputHandler(function () {
            return 'hello';
        });

        $app->output();
        $this->expectOutputString('hello');
    }

    public function testAppTerminateHtml(): void
    {
        $app = $this->getApp();

        $app->terminateHtml('test');
        $this->expectOutputString('test');
    }

    public function testAppTerminateJson(): void
    {
        $app = $this->getApp();
        $testArray = ['str' => 'hello'];

        $app->terminateJson($testArray);
        $this->expectOutputString(Ui::service()->encodeJson($testArray));
    }
}
