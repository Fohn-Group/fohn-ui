<?php

declare(strict_types=1);
/**
 * Create a javascript chainable property.
 * ex: .baz.
 */

namespace Fohn\Ui\Js\Chain;

use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsRenderInterface;

class Property implements Chainable
{
    private JsRenderInterface $name;

    public function __construct(string $name)
    {
        $this->name = Js::var($name);
    }

    public function renderChain(): string
    {
        return $this->name->jsRender();
    }
}
