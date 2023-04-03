<?php

declare(strict_types=1);

/**
 * Render boolean as javascript.
 */

namespace Fohn\Ui\Js\Type;

use Fohn\Ui\Js\JsRenderInterface;

class Boolean extends Type
{
    public static function set(bool $var): JsRenderInterface
    {
        return new static($var);
    }
}
