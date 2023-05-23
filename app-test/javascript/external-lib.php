<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\Js\Jquery;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsChain;
use Fohn\Ui\Js\JsFunction;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;

require_once __DIR__ . '/../init-ui.php';

Ui::page()->includeJsPackage('dayjs', 'https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js');

View\Heading\Header::addTo(Ui::layout(), ['title' => 'Load external library test:', 'size' => 5]);
$clock = View::addTo(Ui::layout())->setTextContent((new \DateTime())->format('H:i:s'))->appendCssClasses('js-clock');
// Use Js::var('') to start a chain with a function call with no parameter: "dayjs()".
$getDayJsDate = JsChain::with('dayjs', Js::var(''))->format('HH:mm:ss');
$fn = JsFunction::anonymous()->execute(Jquery::withView($clock)->text($getDayJsDate));
$useInterval = Js::from('setInterval({{fn}}, 1000)', ['fn' => $fn]);

Ui::page()->appendJsAction($useInterval);
