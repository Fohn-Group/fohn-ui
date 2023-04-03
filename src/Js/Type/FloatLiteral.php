<?php

declare(strict_types=1);

/**
 * Render float for javascript.
 */

namespace Fohn\Ui\Js\Type;

use Fohn\Ui\Js\JsRenderInterface;

class FloatLiteral extends Type
{
    public static function set(float $var): JsRenderInterface
    {
        return new static($var);
    }
}
