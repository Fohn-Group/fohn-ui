<?php

declare(strict_types=1);

/**
 * Generate Default Tailwind utilities to be included in css files via tailwind.config.js.
 */
require_once __DIR__ . '/../vendor/autoload.php';

use Fohn\Ui\Tailwind\Theme\Fohn;

file_put_contents('fohn-theme-tw.txt', Fohn::getThemeCss());
echo 'theme css done!' . \PHP_EOL;
