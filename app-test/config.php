<?php

declare(strict_types=1);
use Fohn\Ui\Service\Ui;

return [
    'env' => Ui::PROD_ENV,
    'timezone' => 'America/Toronto',
    'locale' => 'en_CA',
    'format' => [
        'currency_code' => 'CAD',
        'currency' => '$',
        'date' => 'M d, Y',
        'time' => 'H:i',
        'datetime' => 'M d, Y H:i:s',
    ],
];
