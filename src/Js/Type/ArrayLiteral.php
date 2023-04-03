<?php

declare(strict_types=1);

/**
 * Render PHP non-associative array as javascript Array.
 *
 * Each Array value is render into its own Js type.
 */

namespace Fohn\Ui\Js\Type;

use Fohn\Ui\Js\JsRenderInterface;

class ArrayLiteral extends Type
{
    public static function set(array $var): JsRenderInterface
    {
        if (!empty($var) && $var !== array_values($var)) {
            throw new \Exception('Js array supported non associative array only. Use Js::object instead');
        }

        return new static($var);
    }

    public function jsRender(): string
    {
        if (empty($this->variable)) {
            return '[]';
        }

        $array = [];
        foreach ($this->variable as $value) {
            $array[] = Type::factory($value)->jsRender();
        }

        return '[' . implode(',', $array) . ']';
    }
}
