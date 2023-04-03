<?php

declare(strict_types=1);

/**
 * Render string for javascript.
 */

namespace Fohn\Ui\Js\Type;

use Fohn\Ui\Js\JsRenderInterface;

class StringLiteral extends Type
{
    public static function set(string $var): JsRenderInterface
    {
        return new static($var);
    }

    public function jsRender(): string
    {
        return '\'' . str_replace("'", "\\'", $this->variable ?? '') . '\'';
    }
}
