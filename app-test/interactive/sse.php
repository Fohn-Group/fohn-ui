<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\Callback\ServerEvent;
use Fohn\Ui\Js\Jquery;
use Fohn\Ui\Js\JsStatements;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Tw;
use Fohn\Ui\View;
use Fohn\Ui\View\Button;
use Fohn\Ui\View\GridLayout;
use Fohn\Ui\View\Heading\Header;

require_once __DIR__ . '/../init-ui.php';

Header::addTo(Ui::layout(), ['title' => 'Server side event', 'size' => 4]);

$segment = View\Segment::addTo(Ui::layout())->appendTailwinds([
    'flex',
    'justify-around',
]);

$gridLayout = GridLayout::addTo($segment, ['columns' => 1, 'rows' => 2, 'direction' => 'col']);

$row = View::addTo($gridLayout)->appendTailwinds([Tw::marginX('auto'), Tw::marginY('4')]);
/** @var View\Chip $counter */
$counter = View\Chip::addTo($row, ['color' => 'secondary'])
    ->setTextContent('0')
    ->appendTailwinds(['absolute', 'z-10']);
$ping = View\Chip::addTo($row, ['color' => 'secondary']);

$row = View::addTo($gridLayout)->appendTailwinds([Tw::marginY('auto')]);
$startBtn = Button::addTo($row)->setLabel('Start');
$stopBtn = Button::addTo($row)->setLabel('Stop');
Jquery::onDocumentReady($stopBtn)->attr('disabled', true);

$sse = ServerEvent::addAbstractTo(Ui::layout(), ['keepAlive' => false]);

// Jquery actions to execute when starting ServerSide event.
$startSseEvents = JsStatements::with([
    Jquery::withView($ping)->toggleClass('animate-ping'),
    Jquery::withView($counter->content)->text(0),
    Jquery::withView($startBtn)->attr('disabled', true),
    Jquery::withView($stopBtn)->attr('disabled', false),
]);

// Jquery actions to execute when stopping ServerSide event.
$stopSseEvents = JsStatements::with([
    Jquery::withView($ping)->toggleClass('animate-ping'),
    Jquery::withView($startBtn)->attr('disabled', false),
    Jquery::withView($stopBtn)->attr('disabled', true),
]);

// Fire sse via button.
Jquery::addEventTo($startBtn, 'click')->execute($sse->start($startSseEvents));
Jquery::addEventTo($stopBtn, 'click')->execute($sse->stop($stopSseEvents));

// When user aborted or stop ServerSide event.
$onAborted = function (ServerEvent $sse) {
    error_log(' user aborted ');
};

// When ServerSide event is fire.
$sse->onRequest(function (ServerEvent $sse) use ($counter, $stopSseEvents) {
    for ($i = 1; $i < 26; ++$i) {
        $sse->executeJavascript(Jquery::withView($counter->content)->text($i));
        sleep(1);
        error_log(' counter is ' . $i);
    }
    $sse->executeJavascript($stopSseEvents);
}, [], $onAborted);
