<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Theme\Base;
use Fohn\Ui\Tailwind\Theme\Fohn;
use Fohn\Ui\View;

require_once __DIR__ . '/../init-ui.php';

View::addTo(Ui::layout())->setTextContent('Test View Method');

// Test View method

// add Outside view to the grid.
$view = View::addTo(Ui::layout())->setTextContent('View Text');
$view->appendTailwinds(['m-6', 'p-4', 'w-1/2']);
$view->appendCssClasses('test');
$view->removeTailwind('p-4');
$view->setTextContent('div text <b>content</b>', false);
View::addTo($view)->setTextContent('paragraph text')->setHtmlTag('p');

$tView = View::addTo(Ui::layout());
Fohn::styleAs(Base::CONSOLE, [$tView]);
$tView->setTextContent($view->getHtml());
