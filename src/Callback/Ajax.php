<?php

declare(strict_types=1);

/**
 * Ajax callback terminate execution after callback run using json output.
 */

namespace Fohn\Ui\Callback;

use Fohn\Ui\Js\JsRenderInterface;

class Ajax extends Request implements GuardInterface
{
    protected string $type = self::AJAX_TYPE;

    public function onAjaxPostRequest(\Closure $fx, array $extraOutput = []): self
    {
        $this->execute(function () use ($fx, $extraOutput) {
            $this->verifyCSRF();
            $response = $fx($this->getPostRequestPayload());

            $this->terminateJson(array_merge(['jsRendered' => $response->jsRender()], $extraOutput));
        });

        return $this;
    }

    public function verifyCSRF(): void
    {
        $this->assertSafeRequest();
    }
}
