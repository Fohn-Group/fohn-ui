<?php

declare(strict_types=1);

/**
 * Output Model data via json.
 */

namespace Fohn\Ui\Callback;

class Data extends Request
{
    protected string $type = self::DATA_TYPE;

    public function onDataRequest(\Closure $fx): self
    {
        $this->execute(function () use ($fx) {
            $data = $fx($this->getPostRequestPayload());

            $this->terminateJson(['count' => count($data), 'results' => $data]);
        });

        return $this;
    }
}
