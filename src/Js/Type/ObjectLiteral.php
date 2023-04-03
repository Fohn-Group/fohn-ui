<?php

declare(strict_types=1);
/**
 * Render php associative array as Javascript object.
 * Each associative key is render into Js::var();
 * Each associative value is render into its own Js type.
 */

namespace Fohn\Ui\Js\Type;

use Fohn\Ui\Core\Exception;
use Fohn\Ui\Js\JsRenderInterface;

class ObjectLiteral extends Type
{
    public static function set(array $var): JsRenderInterface
    {
        if (!empty($var) && $var === array_values($var)) {
            throw new Exception('Js object supported associative array only. Use Js::array instead');
        }

        return new static($var);
    }

    public function jsRender(): string
    {
        if (empty($this->variable)) {
            return '{}';
        }

        $array = [];
        foreach ($this->variable as $key => $value) {
            $array[] = Type::factory($key, true)->jsRender() . ':' . Type::factory($value)->jsRender();
        }

        return '{' . implode(',', $array) . '}';
    }
}
