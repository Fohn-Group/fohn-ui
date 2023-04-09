<?php

declare(strict_types=1);

namespace Fohn\Ui\Tests;

use Fohn\Ui\App;
use Fohn\Ui\PageException;
use Fohn\Ui\Service\Data;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tests\Utils\HttpCoverage;

require_once __DIR__ . '/../../app-test/init-autoloader.php';
require_once __DIR__ . '/../init-configuration.php';

$config = loadConfig();

Ui::service()->boot(function (Ui $ui) use ($config) {

    $app = new App(['registerShutdown' => false]);

    HttpCoverage::start();
    $app->onHooks(App::HOOKS_BEFORE_EXIT, function () {
        HttpCoverage::saveData();
    });

    date_default_timezone_set($config['timezone']);
    Data::setDb($config['db']);

    $ui->environment = $config['env'];
    $ui->displayformat = $config['format'];
    $ui->timezone($config['timezone']);

    $ui->setApp($app);
    $ui->appendTemplateDirectories($config['templateDir']);

    // Add default exception handler.
    $ui->setExceptionHandler(PageException::factory());
    // Set demos page.
    $ui->initAppPage(\Fohn\Ui\AppTest\AppTest::createPage($ui->environment));

});
