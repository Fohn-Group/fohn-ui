<?php

declare(strict_types=1);

/**
 * Abstract for javascript type.
 */

namespace Fohn\Ui\Js\Type;

use Fohn\Ui\Js\JsRenderInterface;

abstract class Type implements JsRenderInterface
{
    /** @var mixed */
    protected $variable;

    /**
     * @param mixed $var
     */
    final protected function __construct($var)
    {
        $this->variable = $var;
    }

    /**
     * Return proper js type base on valueType.
     *
     * @param mixed|null $valueType
     */
    public static function factory($valueType, bool $isVariable = false): JsRenderInterface
    {
        if ($valueType === null) {
            return Variable::set('null');
        } elseif (is_string($valueType) && $isVariable) {
            return Variable::set($valueType);
        } elseif (is_int($valueType)) {
            return Integer::set($valueType);
        } elseif (is_bool($valueType)) {
            return Boolean::set($valueType);
        } elseif (is_float($valueType)) {
            return FloatLiteral::set($valueType);
        } elseif (is_array($valueType)) {
            if ($valueType !== array_values($valueType)) {
                return ObjectLiteral::set($valueType);
            }

            return ArrayLiteral::set($valueType);
        } elseif ($valueType instanceof JsRenderInterface) {
            return $valueType;
        }

        return StringLiteral::set($valueType);
    }

    public function jsRender(): string
    {
        return json_encode($this->variable);
    }
}
