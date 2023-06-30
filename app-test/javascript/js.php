<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\Js\Jquery;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsFunction;
use Fohn\Ui\Js\JsReload;
use Fohn\Ui\Js\Type\Variable;
use Fohn\Ui\Service\Theme\Base;
use Fohn\Ui\Service\Theme\Fohn;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;
use Fohn\Ui\View\Button;
use Fohn\Ui\View\Heading\Header;

require_once __DIR__ . '/../init-ui.php';

// create a function under window.arlertFn namespace
// ex: window.alert = (msg) => { alert(msg); }
Ui::page()->appendJsAction(
    Js::from(
        'window.alertFn = {{fn}}',
        [
            'fn' => JsFunction::arrow([Variable::set('msg')])->execute(Js::from('alert(msg)')),
        ]
    )
);

// calling alertFn when button is click with button text content.
$b = Button::addTo(Ui::layout(), ['label' => 'Hello!']);
$b->appendHtmlAttribute('onclick', Js::from('alertFn({{msg}})', ['msg' => Jquery::withView($b)->text()])->jsRender());

// Demonstrates how to use interactive buttons.
Header::addTo(Ui::layout(), ['title' => 'Assign event to View', 'size' => 4]);

// This button hides on page load
$b = Button::addTo(Ui::layout(), ['label' => 'Hidden Button']);
Jquery::onDocumentReady($b)->hide();
Fohn::styleAs(Base::CONSOLE, [View::addTo(Ui::layout(), ['htmlTag' => 'pre'])->setTextContent($b->getJavascript())]);

// This button hides when clicked
$b = Button::addTo(Ui::layout())->setLabel('Hide on click Button');
Jquery::addEventTo($b, 'click')->execute(Jquery::withThis()->hide());
Fohn::styleAs(Base::CONSOLE, [View::addTo(Ui::layout(), ['htmlTag' => 'pre'])->setTextContent($b->getJavascript())]);

$b = Button::addTo(Ui::layout(), ['label' => 'Open in new window with ?foo=bar']);
Jquery::addEventTo($b, 'click')->execute(Ui::jsWindowOpen(Ui::buildUrl(Ui::parseRequestUrl()), ['foo' => 'bar']));

Fohn::styleAs(Base::CONSOLE, [View::addTo(Ui::layout(), ['htmlTag' => 'pre'])->setTextContent($b->getJavascript())]);

Header::addTo(Ui::layout(), ['title' => 'js() method', 'size' => 4]);

$b = Button::addTo(Ui::layout(), ['label' => 'Toggle B']);
$b2 = Button::addTo(Ui::layout(), ['label' => 'B']);

Jquery::addEventTo($b, 'click')
    ->executes(
        [
            JQuery::withView($b2)->toggle(),
            Js::from('console.log("Button B is now toggle")'),
            Js::from('console.log("event: ", e)'),
        ]
    );

Fohn::styleAs(Base::CONSOLE, [View::addTo(Ui::layout(), ['htmlTag' => 'pre'])->setTextContent($b->getJavascript())]);

Header::addTo(Ui::layout(), ['title' => 'Callbacks', 'size' => 4]);

// On button click reload it and change it's title
$b = Button::addTo(Ui::layout(), ['label' => 'Callback Test']);
Jquery::jqCallback($b, 'click', function ($jquery, $payload) {
    return $jquery->text(random_int(1, 20) . ' id = ' . $payload['id']);
}, ['id' => 2]);
Fohn::styleAs(Base::CONSOLE, [View::addTo(Ui::layout(), ['htmlTag' => 'pre'])->setTextContent($b->getJavascript())]);

$l = $_GET['test'] ?? '';

$b = Button::addTo(Ui::layout(), ['label' => 'Reload ' . $l]);
Jquery::addEventTo($b, 'click')->execute(JsReload::view($b, ['test ' => random_int(0, 100)])->afterSuccess(Js::from('console.log("reloaded1")')));

$b = Button::addTo(Ui::layout(), ['label' => 'Reload ' . $l]);
Jquery::addEventTo($b, 'click')->execute(JsReload::view($b, ['test ' => random_int(0, 100)])->afterSuccess(Js::from('console.log("reloaded2")')));

$b = Button::addTo(Ui::layout(), ['label' => 'Reload  ' . $l]);
Jquery::addEventTo($b, 'click')->execute(JsReload::view($b, ['test ' => random_int(0, 100)])->afterSuccess(Js::from('console.log("reloaded3")')));
