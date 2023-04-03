<?php

declare(strict_types=1);

namespace Fohn\Ui\Tests;

use Fohn\Ui\App;
use Fohn\Ui\PageException;
use Fohn\Ui\Service\Data;
use Fohn\Ui\Service\Ui;

require_once __DIR__ . '/../../app-test/init-autoloader.php';
require_once __DIR__ . '/init-configuration.php';

if (!ui::isBooted()) {
    $config = loadConfig();
    bootTestUi($config);
}

function bootTestUi(array $config): void
{
    $app = new App(['registerShutdown' => false]);

    if (file_exists(__DIR__ . '/../../tools/CoverageUtil.php')) {
        require_once __DIR__ . '/../../tools/CoverageUtil.php';
        \CoverageUtil::start();
        $app->onHooks(App::HOOKS_BEFORE_EXIT, function () {
            \CoverageUtil::saveData();
        });
    }

    date_default_timezone_set($config['timezone']);
    Data::setDb($config['db']);

    // Create service.
    $ui = Ui::service();
    $ui->environment = $config['env'];
    $ui->displayformat = $config['format'];
    $ui->timezone($config['timezone']);

    $ui->setApp($app);
    $ui->appendTemplateDirectories($config['templateDir']);

    // Add default exception handler.
    $ui->setExceptionHandler(PageException::factory());
    // Set demos page.
    $ui->initAppPage(\Fohn\Ui\AppTest\AppTest::createPage($ui->environment));

    $ui->markAsBooted();
}
