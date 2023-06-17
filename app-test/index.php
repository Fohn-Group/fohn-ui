<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;
use Fohn\Ui\View\Heading\Header;

require_once __DIR__ . '/init-ui.php';

Header::addTo(Ui::layout(), ['title' => 'Fohn Ui Test', 'size' => 4]);

View::addTo(Ui::layout())->setTextContent('Testing application for Fohn-Ui views and components.');
