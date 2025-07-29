<?php

declare(strict_types=1);

return [
    'env' => 'test',
    'templateDir' => [dirname(__DIR__, 1) . \DIRECTORY_SEPARATOR . 'template' . \DIRECTORY_SEPARATOR . 'tailwind'],
    'timezone' => 'America/Toronto',
    'format' => [
        'currency' => '$',
        'date' => 'M d, Y',
        'time' => 'H:i',
        'datetime' => 'M d, Y H:i:s',
    ],
    'base_uri' => 'http://127.0.0.1:7000',
];
