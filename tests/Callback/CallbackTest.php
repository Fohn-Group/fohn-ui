<?php

declare(strict_types=1);
/**
 * Callbacks Trigger test.
 */

namespace Fohn\Ui\Tests\Callback;

use Fohn\Ui\Callback\Ajax;
use Fohn\Ui\Callback\Data;
use Fohn\Ui\Callback\Generic;
use Fohn\Ui\Callback\JqReload;
use Fohn\Ui\Callback\Jquery;
use Fohn\Ui\Callback\ServerEvent;
use Fohn\Ui\HtmlTemplate;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;

class CallbackTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $ui = Ui::service();
        $ui->setApp(new MockApp(['registerShutdown' => false]));
    }

    public function testDataCallback(): void
    {
        $hasExecute = false;
        $trigger = 'data_tg';
        $cb = new Data(['urlTrigger' => $trigger]);
        // should not trigger
        $cb->onDataRequest(function () use (&$hasExecute) {
            $hasExecute = true;

            return [];
        });
        $this->assertFalse($hasExecute);

        // should trigger
        $_GET[Generic::URL_QUERY_TARGET] = $trigger;
        $_GET[$trigger] = Generic::DATA_TYPE;

        $cb->onDataRequest(function () use (&$hasExecute) {
            $hasExecute = true;

            return [];
        });
        $this->assertTrue($hasExecute);
    }

    public function testGenericCallback(): void
    {
        $hasExecute = false;
        $trigger = 'generic_tg';
        $cb = new Generic(['urlTrigger' => $trigger]);
        // should not trigger
        $cb->onRequest(function () use (&$hasExecute) {
            $hasExecute = true;

            return null;
        });
        $this->assertFalse($hasExecute);

        // should trigger
        $_GET[Generic::URL_QUERY_TARGET] = $trigger;
        $_GET[$trigger] = Generic::GENERIC_TYPE;

        $cb->onRequest(function () use (&$hasExecute) {
            $hasExecute = true;

            return null;
        });
        $this->assertTrue($hasExecute);
    }

    public function testAjaxCallback(): void
    {
        $hasExecute = false;
        $trigger = 'ajax_tg';
        $cb = new Ajax(['urlTrigger' => $trigger]);
        // should not trigger
        $cb->onAjaxPostRequest(function () use (&$hasExecute) {
            $hasExecute = true;

            return null;
        });
        $this->assertFalse($hasExecute);

        // should trigger
        $_GET[Generic::URL_QUERY_TARGET] = $trigger;
        $_GET[$trigger] = Generic::AJAX_TYPE;

        $cb->onAjaxPostRequest(function () use (&$hasExecute) {
            $hasExecute = true;

            return Js::from('');
        });
        $this->assertTrue($hasExecute);
    }

    public function testJqueryCallback(): void
    {
        $hasExecute = false;
        $trigger = 'jquery_tg';
        $cb = new Jquery(['urlTrigger' => $trigger]);
        // should not trigger
        $cb->onJqueryRequest(function () use (&$hasExecute) {
            $hasExecute = true;

            return null;
        });
        $this->assertFalse($hasExecute);

        // should trigger
        $_GET[Generic::URL_QUERY_TARGET] = $trigger;
        $_GET[$trigger] = Generic::JQUERY_TYPE;

        $cb->onJqueryRequest(function () use (&$hasExecute) {
            $hasExecute = true;

            return Js::from('');
        });
        $this->assertTrue($hasExecute);
    }

    public function testJqReloadCallback(): void
    {
        $hasExecute = false;
        $trigger = 'jr_reload_tg';
        $view = new View(['template' => new HtmlTemplate('<div></div>')]);
        $cb = JqReload::addAbstractTo($view, ['urlTrigger' => $trigger]);

        // should not trigger
        $cb->onJqueryRequest(function () use (&$hasExecute) {
            $hasExecute = true;

            return null;
        });
        $this->assertFalse($hasExecute);

        // should trigger
        $_GET[Generic::URL_QUERY_TARGET] = $trigger;
        $_GET[$trigger] = Generic::JQUERY_TYPE;

        $cb->onJqueryRequest(function () use (&$hasExecute) {
            $hasExecute = true;
        });
        $this->assertTrue($hasExecute);
    }

    public function testServerEventCallback(): void
    {
        $hasExecute = false;
        $trigger = 'sse_tg';
        $cb = new ServerEvent(['urlTrigger' => $trigger, 'app' => new MockApp()]);
        $cb->invokeInitRenderTree();

        // should not trigger
        $cb->onRequest(function () use (&$hasExecute) {
            $hasExecute = true;

            return null;
        });
        $this->assertFalse($hasExecute);

        // should trigger
        $_GET[Generic::URL_QUERY_TARGET] = $trigger;
        $_GET[$trigger] = Generic::SERVER_EVENT_TYPE;

        $cb->onRequest(function (ServerEvent $event) use (&$hasExecute) {
            $hasExecute = true;
            $event->executeJavascript(Js::from('console.log()'));
        });
        $this->assertTrue($hasExecute);
    }
}
