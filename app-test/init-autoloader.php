<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

$isRootProject = file_exists(__DIR__ . '/../vendor/autoload.php');

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require dirname(__DIR__, $isRootProject ? 1 : 4) . '/vendor/autoload.php';

$loader->setClassMapAuthoritative(false);
$loader->setPsr4('Fohn\Ui\AppTest\\', __DIR__ . '/_includes');
unset($isRootProject, $loader);
