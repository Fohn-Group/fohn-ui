<?php

declare(strict_types=1);

namespace Fohn\Ui\Tests;

use Fohn\Ui\Service\Ui;
use PHPUnit\Framework\TestCase;

class FohnTestCase extends TestCase
{
    public function initUiService(): void
    {
        $ui = Ui::service();
        $ui->appendTemplateDirectories([dirname(__DIR__) . \DIRECTORY_SEPARATOR . 'template' . \DIRECTORY_SEPARATOR . 'tailwind']);
    }
}
