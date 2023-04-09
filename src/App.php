<?php

declare(strict_types=1);

namespace Fohn\Ui;

use Fohn\Ui\App\ResponseEmitter;
use Fohn\Ui\Core\HookFn;
use Fohn\Ui\Core\HookTrait;
use Fohn\Ui\Core\InjectorTrait;
use Fohn\Ui\Service\Ui;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class App
{
    use HookTrait;
    use InjectorTrait;

    public const HOOKS_BEFORE_EXIT = self::class.'@beforeExit';
    public const HOOKS_BEFORE_OUTPUT = self::class . '@beforeOutput';

    /** @var \Closure Function that will set final html output. */
    private \Closure $outputHandler;

    /** Will set register_shutdown_function that call outputHandler for HTML content. */
    protected bool $registerShutdown = true;

    private bool $hasRun = false;

    protected ?ResponseEmitter $emitter = null;
    protected bool $callExit = true;

    /**
     * Will provide a default outputHandler with default output.
     * However, Ui service must set the output handler with something meaningful when
     * registerShutdown() method is used. This is usually the top Page view that is rendered as HTML.
     *
     * When $outputOnExit is true, the constructor will register a shutdown
     * function in order to output Html via the outputHandler and send it
     * to the browser client when script exit.
     */
    public function __construct(array $defaults = [])
    {
        $this->injectDefaults($defaults);

        if (!$this->emitter) {
            $this->emitter = new ResponseEmitter();
        }

        $this->setOutputHandler(function () {
            return 'App Output handler is not set.';
        });

        if ($this->registerShutdown) {
            $this->registerShutdown();
        }
    }

    public function setOutputHandler(\Closure $fx): self
    {
        $this->outputHandler = $fx;

        return $this;
    }

    public function callExit(): void
    {
        $this->hasRun = true;
        $this->callHooks(self::HOOKS_BEFORE_EXIT, HookFn::withVoid([$this]));

        if ($this->callExit) {
            exit;
        }
    }

    /**
     * Return html output for terminating request.
     */
    public function getHtmlOutput(): string
    {
        $this->callHooks(self::HOOKS_BEFORE_OUTPUT, HookFn::withVoid([$this]));

        return ($this->outputHandler)();
    }

    /**
     * Output HTML to client.
     */
    public function output(): void
    {
        $this->terminateHtml($this->getHtmlOutput());
    }

    public function getResponse(int $statusCode = 200, array $header = []): Response
    {
        $response = (new Response())
            ->withStatus($statusCode)
            ->withHeader(...$header);

        return $response;
    }

    public function getHtmlResponse(string $html, int $statusCode = 200): Response
    {
        $response = $this->getResponse($statusCode, ['content-type', 'text/html']);
        $response->getBody()->write($html);

        return $response;
    }

    public function terminateHtml(string $html, int $statusCode = 200): void
    {
        $this->terminateAndExit($this->getHtmlResponse($html, $statusCode));
    }

    public function getJsonResponse(array $output, int $statusCode = 200): Response
    {
        $response = $this->getResponse($statusCode, ['content-type', 'application/json']);
        $response->getBody()->write(Ui::service()->encodeJson($output));

        return $response;
    }

    public function terminateJson(array $output, int $statusCode = 200): void
    {
        $this->terminateAndExit($this->getJsonResponse($output, $statusCode));
    }

    /**
     *  Emit $response and exit.
     */
    public function terminateAndExit(ResponseInterface $response): void
    {
        $this->emitter->emit($response);

        $this->callExit();
    }

    protected function registerShutdown(): void
    {
        register_shutdown_function(
            function () {
                if (!$this->hasRun) {
                    try {
                        $this->output();
                    } catch (\Throwable $e) {
                        $this->handleException($e);
                    }
                }
            }
        );
    }

    protected function handleException(\Throwable $exception): void
    {
        if (Ui::service()->isAjaxRequest()) {
            $this->terminateJson([Ui::EXCEPTION_OUTPUT_KEY => Ui::service()->renderExceptionAsHtml($exception)], 500);
        } else {
            $output = Ui::service()->renderExceptionAsHtml($exception);
            $this->terminateHtml($output, 500);
        }
    }

    public function isConnectionAborted(): int
    {
        return connection_aborted();
    }

    /**
     * Output Server side event to client.
     */
    public function streamEvent(array $event): void
    {
        foreach ($event as $v) {
            echo $v;
        }

        flush();
    }

    /**
     * Prepare output for Server Side Event Streaming.
     */
    public function prepareEventStreaming(int $limit = 0, bool $ignoreUserAbort = true): void
    {
        @set_time_limit($limit);
        ignore_user_abort($ignoreUserAbort);

        $response = (new Response())
            ->withHeader('Content-Type', 'text/event-stream')
            ->withHeader('Cache-Control', 'no-cache')
            ->withHeader('X-Accel-Buffering', 'no')
            ->withStatus(200);

        $this->emitter->emit($response);
    }
}
