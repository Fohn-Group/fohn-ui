<?php

declare(strict_types=1);

namespace Fohn\Ui\Callback;

// Ajax callback fire by Ajaxec jQuery plugin.

use Fohn\Ui\Js;
use Fohn\Ui\Js\JsRenderInterface;

class Jquery extends Request implements JsRenderInterface
{
    protected string $type = self::JQUERY_TYPE;

    /** Payload data to include in ajax request. */
    protected array $requestPayload = [];

    // TODO is still working?
    /** Text to display as a confirmation. Set with setConfirm(..). */
    public ?string $confirm = null;

    // TODO confirm for axios
    /** Use this . */
    public array $apiConfig = [];

    /** Include web storage data item (key) value to be include in the request. */
    public ?string $storeName = null;

    /** Allow to trigger on reload or not. */
    public bool $triggerOnReload = false;

    /**
     * Set a confirmation to be displayed before actually sending a request.
     */
    public function setConfirm(string $text = 'Are you sure?'): void
    {
        $this->confirm = $text;
    }

    /**
     * Execute callback function using requestPayload attached to callback.
     */
    public function onJqueryRequest(\Closure $fx, array $requestPayload = []): self
    {
        $this->requestPayload = $requestPayload;

        $this->execute(function () use ($fx) {
            $response = $fx($this->getPostRequestPayload());

            $this->terminateJson(['jsRendered' => $response->jsRender()]);
        });

        return $this;
    }

    public function jsRender(): string
    {
        // @phpstan-ignore-next-line
        return Js\Jquery::withThis()->fohnCallbackRequest([
            'uri' => $this->getUrl(),
            'payload' => $this->requestPayload,
            'confirm' => $this->confirm,
            'fetchOptions' => $this->apiConfig,
            'storeName' => $this->storeName,
        ])->jsRender();
    }
}
