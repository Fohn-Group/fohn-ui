<?php

declare(strict_types=1);

/**
 * Callback that will reload the owner view.
 */

namespace Fohn\Ui\Callback;

use Fohn\Ui\Js;
use Fohn\Ui\Js\JsRenderInterface;

class JqReload extends Jquery
{
    /** Javascript to execute after reload is complete and onSuccess is executed. */
    private ?JsRenderInterface $afterSuccess = null;

    private bool $includeStorage = false;
    private string $loadContext = '';

    public function setAfterSuccess(JsRenderInterface $expression): void
    {
        $this->afterSuccess = $expression;
    }

    public function includeStorage(): void
    {
        $this->includeStorage = true;
    }

    public function onJqueryRequest(\Closure $fx, array $requestPayload = []): self
    {
        $this->requestPayload = $requestPayload;

        $this->execute(function () use ($fx) {
            $response = $fx($this->getPostRequestPayload());

            $this->terminateJson($this->getOwner()->renderToJsonArr());
        });

        return $this;
    }

    public function jsRender(): string
    {
        return Js\Jquery::withThis()
            ->fohnReloadView(
                [
                    'uri' => $this->getUrl(),
                    'payload' => $this->requestPayload,
                    'afterSuccess' => $this->afterSuccess ? $this->afterSuccess->jsRender() : null,
                    'fetchOptions' => $this->apiConfig,
                    'loadContext' => $this->loadContext ?: null,
                    'storeName' => $this->includeStorage ? $this->getOwner()->getIdAttribute() : null,
                ]
            )
            ->jsRender();
    }
}
