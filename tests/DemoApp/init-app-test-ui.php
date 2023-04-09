<?php

declare(strict_types=1);

namespace Fohn\Ui\Tests;

use Atk4\Data\Persistence\Sql;
use Fohn\Ui\App;
use Fohn\Ui\PageException;
use Fohn\Ui\Service\Data;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tests\Utils\HttpCoverage;

//require_once __DIR__ . '/../../app-test/init-autoloader.php';

//if (!ui::isBooted()) {
//    bootTestUi(loadConfig());
//}

//function bootTestUi(array $config): void
//{
//    $app = new App(['registerShutdown' => false]);
//
//    date_default_timezone_set($config['timezone']);
//    Data::setDb($config['db']);
//
//    // Create service.
//    $ui = Ui::service();
//    $ui->environment = $config['env'];
//    $ui->displayformat = $config['format'];
//    $ui->timezone($config['timezone']);
//
//    $ui->setApp($app);
//    $ui->appendTemplateDirectories($config['templateDir']);
//
//    // Add default exception handler.
//    $ui->setExceptionHandler(PageException::factory());
//    // Set demos page.
//    $ui->initAppPage(\Fohn\Ui\AppTest\AppTest::createPage($ui->environment));
//
//    $ui->markAsBooted();
//}
//
//function loadConfig(): array
//{
//    /** @var array $config */
//    $config = require __DIR__ . '/../ui-test-config.php';
//
//    // Override some value using local config if needed.
//    if (file_exists(__DIR__ . '/../ui-test-config.local.php')) {
//        $config = array_merge($config, require __DIR__ . '/../ui-test-config.local.php');
//    }
//
//    // Create a default $config['db'] using sqlite if not set but make sure db file is present.
//    if (!isset($config['db'])) {
//        if (!file_exists(__DIR__ . '/../_data/db.sqlite')) {
//            throw new \Error('Db file is not present. Please create the file using create-sqlite.php script in _demo-data folder.');
//        }
//
//        $config['db'] = new Sql('sqlite:' . __DIR__ . '/../_data/db.sqlite');
//    }
//
//    return $config;
//}
