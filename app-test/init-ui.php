<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Atk4\Data\Persistence\Sql;
use Fohn\Ui\App;
use Fohn\Ui\PageException;
use Fohn\Ui\Service\Data;
use Fohn\Ui\Service\Ui;

require_once __DIR__ . '/init-autoloader.php';

if (!Ui::isBooted()) {
    bootUi(loadConfig());
}

function bootUi(array $config): void
{
    date_default_timezone_set($config['timezone']);
    Data::setDb($config['db']);

    // Create service.
    $ui = Ui::service();
    $ui->environment = $config['env'];
    $ui->displayformat = array_merge($ui->displayformat, $config['format']);
    $ui->locale($config['locale']);
    $ui->timezone($config['timezone']);

    $ui->setApp(new App());

    // Add default exception handler.
    $ui->setExceptionHandler(PageException::factory());
    // Set demos page.
    $ui->initAppPage(AppTest::createPage($ui->environment));
}

function loadConfig(): array
{
    /** @var array $config */
    $config = require_once __DIR__ . '/config.php';

    // Override some value using local config if needed.
    if (file_exists(__DIR__ . '/../local/config.local.php')) {
        $config = array_merge($config, require_once __DIR__ . '/../local/config.local.php');
    }

    // Create a default $config['db'] using sqlite if not set but make sure db file is present.
    if (!isset($config['db'])) {
        if (!file_exists(__DIR__ . '/_app-data/db.sqlite')) {
            throw new \Error('Db file is not present. Please create the file using create-sqlite.php script in _app-data folder.');
        }

        $config['db'] = new Sql('sqlite:' . __DIR__ . '/_app-data/db.sqlite');
    }

    return $config;
}
