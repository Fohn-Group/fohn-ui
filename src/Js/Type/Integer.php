<?php

declare(strict_types=1);

/**
 * Render integer for javascript.
 */

namespace Fohn\Ui\Js\Type;

use Fohn\Ui\Js\JsRenderInterface;

class Integer extends Type
{
    public static function set(int $var): JsRenderInterface
    {
        return new static($var);
    }
}
