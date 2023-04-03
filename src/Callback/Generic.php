<?php

declare(strict_types=1);

namespace Fohn\Ui\Callback;

/**
 * Add this object to your render tree, so it will expose a unique URL which, when
 * executed directly will execute a php function.
 *
 * Callback function run when triggered, i.e. when it's urlTrigger param value is present in the $_GET request.
 * The current callback will be set within the $_GET[Callback::URL_QUERY_TARGET] and will be set to urlTrigger as well.
 */
class Generic extends Request
{
    /**
     * Executes user-specified action when call-back is triggered.
     */
    public function onRequest(\Closure $fx): void
    {
        $this->execute(function () use ($fx) {
            return $fx($this->getPostRequestPayload());
        });
    }
}
