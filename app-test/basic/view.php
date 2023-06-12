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

View::addTo(Ui::layout())->setTextContent('Output should not have p4 in class.');

$view = (new View())->setTextContent('View Text');
$view->appendTailwinds(['m-6', 'p-4', 'w-1/2']);
$view->appendCssClasses('test');
$view->removeTailwind('p-4');

$tView = View::addTo(Ui::layout());
Fohn::styleAs(Base::CONSOLE, [$tView]);
$tView->setTextContent($view->getHtml());

// display sanitize content.
View::addTo(Ui::layout())->setTextContent('Output sanitized <b>content</b>.')->setHtmlTag('p');

// display as html content.
View::addTo(Ui::layout())->setTextContent('Ouput un-sanitize <b>content</b>', false);
