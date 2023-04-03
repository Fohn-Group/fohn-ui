<?php

declare(strict_types=1);

/**
 * Render string as a javascript variable.
 */

namespace Fohn\Ui\Js\Type;

use Fohn\Ui\Js\JsRenderInterface;

class Variable extends Type
{
    public static function set(string $var): JsRenderInterface
    {
        return new static($var);
    }

    public function jsRender(): string
    {
        return $this->variable;
    }
}
