<?php

declare(strict_types=1);

use Atk4\Data\Persistence\Sql;

function loadConfig(): array
{
    /** @var array $config */
    $config = require __DIR__ . '/ui-test-config.php';

    // Override some value using local config if needed.
    if (file_exists(__DIR__ . '/ui-test-config.local.php')) {
        $localConfig = require __DIR__ . '/ui-test-config.local.php';
        $config = array_merge($config, $localConfig);
    }

    // Create a default $config['db'] using sqlite if not set but make sure db file is present.
    if (!isset($config['db'])) {
        if (!file_exists(__DIR__ . '/_data/db.sqlite')) {
            throw new \Error('Db file is not present. Please create the file using create-sqlite.php script in _demo-data folder.');
        }

        $config['db'] = new Sql('sqlite:' . __DIR__ . '/_data/db.sqlite');
    }

    return $config;
}
