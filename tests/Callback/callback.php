<?php

declare(strict_types=1);
/**
 * Callbacks Test.
 */

use Fohn\Ui\Callback\Ajax;
use Fohn\Ui\Callback\Data;
use Fohn\Ui\Callback\Generic;
use Fohn\Ui\Callback\JqReload;
use Fohn\Ui\Callback\Jquery;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tests\Callback\CallbackPayloadTest;
use Fohn\Ui\View;

require_once __DIR__ . '/init-test-callback-ui.php';

$dataCallback = Data::addAbstractTo(Ui::layout(), ['urlTrigger' => 'data_tg']);
$dataCallback->onDataRequest(function (array $payload) {
    return array_merge($payload, ['d1' => 'test1']);
});

$ajaxCallbak = Ajax::addAbstractTo(Ui::layout(), ['urlTrigger' => 'ajax_tg']);
$ajaxCallbak->onAjaxPostRequest(function (array $payload) {
    return Js::from('console.log({{var}})', ['var' => Js::var($payload['p1'])]);
});

$jQueryCallback = Jquery::addAbstractTo(Ui::layout(), ['urlTrigger' => 'jquery_tg']);
$jQueryCallback->onJqueryRequest(function (array $payload) {
    return Js::from('console.log({{var}})', ['var' => Js::var($payload['p1'])]);
});

$v = View::addTo(Ui::layout());
$v->setIdAttribute('v-test');

$jQueryReload = JqReload::addAbstractTo($v, ['urlTrigger' => 'jq_reload_tg']);
$jQueryReload->onJqueryRequest(function ($payload) use ($v) {
    $v->setHtmlContent($payload['p1']);
});

$genericCallback = Generic::addAbstractTo(Ui::layout(), ['urlTrigger' => 'generic_tg']);
$genericCallback->onRequest(Closure::fromCallable([new CallbackPayloadTest(), 'executeGenericCallback']));
