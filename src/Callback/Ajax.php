<?php

declare(strict_types=1);

/**
 * Ajax callback terminate execution after callback run using json output.
 */

namespace Fohn\Ui\Callback;

class Ajax extends Request
{
    protected string $type = self::AJAX_TYPE;

    public function onAjaxPostRequest(\Closure $fx): self
    {
        $this->execute(function () use ($fx) {
            $response = $fx($this->getPostRequestPayload());

            $this->terminateJson(['jsRendered' => $response->jsRender()]);
        });

        return $this;
    }
}
