<?php

declare(strict_types=1);

/**
 * Execute callback function.
 */

namespace Fohn\Ui\Callback;

use Fohn\Ui\AbstractView;
use Fohn\Ui\Core\AbstractViewHelperTrait;
use Fohn\Ui\Core\Exception;
use Fohn\Ui\Service\Ui;

class Request extends AbstractView
{
    use AbstractViewHelperTrait;

    public const URL_QUERY_TARGET = '__qcb_';
    public const AJAX_TYPE = '__ajax';
    public const DATA_TYPE = '__data';
    public const JQUERY_TYPE = '__jq';
    public const GENERIC_TYPE = '__cb';
    public const SERVER_EVENT_TYPE = '__sse';

    /** Redirect url if csfr verification fail. */
    private static ?string $csfrRedirectUrl = null;
    private static bool $guard = false;

    protected string $type = self::GENERIC_TYPE;

    /** Specify a custom GET trigger. */
    protected ?string $urlTrigger = null;

    /** Store currently running callback arguments. */
    protected static array $runningCallbackArgs = [];

    public static function getRunningCallbackArgs(): array
    {
        return static::$runningCallbackArgs;
    }

    public static function protect(string $redirectUrl = null): void
    {
        self::$guard = true;
        self::$csfrRedirectUrl = $redirectUrl;
    }

    /**
     * Initialization.
     */
    protected function initRenderTree(): void
    {
        parent::initRenderTree();

        $this->setUrlTrigger($this->urlTrigger);
    }

    public function setUrlTrigger(string $trigger = null): void
    {
        if (!$trigger && strlen($this->getViewId()) > 10) {
            $this->urlTrigger = Ui::service()->factoryId($this->getViewId());
        } elseif (!$trigger) {
            $this->urlTrigger = $this->getViewId();
        } else {
            $this->urlTrigger = $trigger;
        }
    }

    public function getUrlTrigger(): string
    {
        return $this->urlTrigger;
    }

    /**
     * Only execute callback function if triggered.
     * i.e urlTrigger is set and callback canExecute.
     *
     * @return mixed|void
     */
    protected function execute(\Closure $fx, array $params = [])
    {
        if ($this->isTriggered() && $this->canExecute()) {
            static::$runningCallbackArgs[$this->urlTrigger] = $this->getTriggeredValue();

            return $fx(...$params);
        }

        return null;
    }

    protected function assertSafeRequest(): void
    {
        if (!self::$guard) {
            return;
        }

        $requestToken = Ui::service()->serverRequest()->getHeaderLine('X-CSFR-TOKEN');
        $saveToken = Ui::session()->get(Ui::TOKEN_KEY_NAME);

        if (!$requestToken || ($requestToken !== $saveToken)) {
            if (self::$csfrRedirectUrl) {
                $this->terminateJson(['success' => true, 'jsRendered' => Ui::jsRedirect(self::$csfrRedirectUrl)->jsRender()]);
            }
            throw new Exception('Access denied.');
        }
    }

    /**
     * Return true if urlTrigger is part of the request.
     */
    public function isTriggered(): bool
    {
        return isset($_GET[$this->urlTrigger]) && $this->getTriggeredValue() === $this->type;
    }

    /**
     * Return callback triggered value.
     */
    public function getTriggeredValue(): string
    {
        return $_GET[$this->urlTrigger] ?? '';
    }

    /**
     * Only current callback can terminate.
     */
    public function canTerminate(): bool
    {
        return isset($_GET[static::URL_QUERY_TARGET]) && $_GET[static::URL_QUERY_TARGET] === $this->urlTrigger;
    }

    /**
     * Delegate can execute responsibility to Sub class.
     */
    protected function canExecute(): bool
    {
        return true;
    }

    /**
     * Return URL that will trigger action on this call-back.
     */
    public function getUrl(bool $includeParam = true): string
    {
        return Ui::buildUrl(Ui::parseRequestUrl(), $includeParam ? $this->getUrlArguments() : []);
    }

    /**
     * Terminate this callback using an array.
     */
    public function terminateJson(array $output): void
    {
        if ($this->canTerminate()) {
            Ui::service()->terminateJson(array_merge(['success' => true], $output));
        }
    }

    protected function getPostRequestPayload(): array
    {
        return Ui::service()->getInput();
    }

    /**
     * Return proper url argument for this callback.
     */
    protected function getUrlArguments(): array
    {
        return array_merge(
            static::getRunningCallbackArgs(),
            [static::URL_QUERY_TARGET => $this->urlTrigger, $this->urlTrigger => $this->type],
            $this->getOwner()->getUrlStickyArgs()
        );
    }

    public static function assertNoCallbackRunning(): void
    {
        if (isset($_GET[static::URL_QUERY_TARGET])) {
            throw (new Exception('Callback requested, but never reached. You may be missing some arguments in request URL.'))
                ->addMoreInfo('callback requested', $_GET[static::URL_QUERY_TARGET]);
        }
    }
}
