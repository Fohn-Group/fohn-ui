<?php

declare(strict_types=1);

/**
 * Server Event Callback.
 */

namespace Fohn\Ui\Callback;

use Fohn\Ui\App;
use Fohn\Ui\Core\Exception;
use Fohn\Ui\Core\HookFn;
use Fohn\Ui\Core\HookTrait;
use Fohn\Ui\Js\JsChain;
use Fohn\Ui\Js\JsRenderInterface;
use Fohn\Ui\Js\JsStatements;
use Fohn\Ui\Service\Ui;

class ServerEvent extends Generic
{
    use HookTrait;

    protected string $type = self::SERVER_EVENT_TYPE;

    /** Executed when user aborted, or disconnect browser, when using this SSE. */
    public const HOOK_ABORTED = self::class . '@connection_aborted';
    public const EVENT_NAME = 'fohn_sse_action';
    public const SSE_JS_EVENT_ID = 'js';

    /**
     * Stop and delete EventSource object prior to closing browser window.
     * Add window.beforeunload listener for closing js EventSource.
     * Off by default.
     */
    public bool $closeBeforeUnload = false;

    /** Keep execution alive or not if connection is close by user. False mean that execution will stop on user aborted. */
    public bool $keepAlive = false;

    /**
     * The min size of event stream data buffer.
     * Set it to 4096 when using phpfpm with apache mod_proxy_fcgi.
     */
    public int $minBufferSize = 0;

    /** Check if ServerEvent callback has been request and executed. */
    private bool $requestSet = false;

    protected ?App $app = null;

    protected function initRenderTree(): void
    {
        parent::initRenderTree();
        if (!$this->app) {
            $this->initStreamingApp();
        }
    }

    /**
     * Initialise this sse.
     * It will ignore user abort by default.
     */
    protected function initStreamingApp(): void
    {
        $this->app = new App(['registerShutdown' => false]);
    }

    public function onRequest(\Closure $fx, array $params = [], \Closure $onAbortedFx = null): void
    {
        if ($onAbortedFx) {
            $this->onAborted($onAbortedFx);
        }

        try {
            $this->requestSet = true;

            $this->execute(function () use ($fx, $params) {
                $this->app->prepareEventStreaming();

                $fx(...array_merge([$this], $params));

                $this->app->callExit();
            }, array_merge([$this], $params));
        } catch (\Throwable $exception) {
            $this->app->terminateHtml(Ui::service()->renderExceptionAsHtml($exception), 500);
        }
    }

    /**
     * A function that get executes when user aborted or disconnect browser.
     * Must be called prior to onRequestMethod.
     */
    public function onAborted(callable $fx): void
    {
        if ($this->requestSet) {
            throw new Exception('onAborted function should be set prior to run Server Event callback.');
        }

        $this->onHook(self::HOOK_ABORTED, $fx);
    }

    public function executeJavascript(JsRenderInterface $statements): void
    {
        $this->sendEvent(
            self::SSE_JS_EVENT_ID,
            Ui::service()->encodeJson(['success' => true, 'message' => 'Success', 'jsRendered' => $statements->jsRender()]),
            self::EVENT_NAME
        );
    }

    /**
     * Output a SSE Event.
     */
    public function sendEvent(string $id, string $event, string $eventName): void
    {
        $this->sendBlock($id, $event, $eventName);
    }

    /**
     * Send a SSE data block.
     */
    protected function sendBlock(string $id, string $event, string $name): void
    {
        if ($this->app->isConnectionAborted()) {
            // stop execution when aborted if not keepAlive.
            if (!$this->keepAlive) {
                $this->callHook(self::HOOK_ABORTED, HookFn::withVoid([$this]));
                $this->app->callExit();
            }
        }

        $streamEvent = [
            'id: ' . $id . "\n",
            'event: ' . $name . "\n",
            'data' => $this->wrapEvent($event) . "\n",
        ];

        $this->app->streamEvent($streamEvent, $this->minBufferSize);
    }

    public function start(JsStatements $statements = null): JsRenderInterface
    {
        if (!$statements) {
            $statements = JsStatements::with([]);
        }
        $options['uri'] = $this->getUrl();
        $options['closeBeforeUnload'] = $this->closeBeforeUnload;
        // @phpstan-ignore-next-line
        $statements->addStatement(JsChain::withUiLibrary()->serverEventService->start($this->getUrlTrigger(), $options));

        return $statements;
    }

    public function stop(JsStatements $statements = null): JsRenderInterface
    {
        if (!$statements) {
            $statements = JsStatements::with([]);
        }
        // @phpstan-ignore-next-line
        $statements->addStatement(JsChain::withUiLibrary()->serverEventService->stop($this->getUrlTrigger()));

        return $statements;
    }

    /**
     * Create SSE event string.
     */
    private function wrapEvent(string $string): string
    {
        return implode('', array_map(function ($v) {
            return 'data: ' . $v . "\n";
        }, preg_split('~\r?\n|\r~', $string)));
    }
}
