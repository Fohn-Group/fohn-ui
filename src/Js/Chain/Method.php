<?php

declare(strict_types=1);
/**
 * Create a javascript chainable method.
 * ex: methodName(arg1, arg2).
 */

namespace Fohn\Ui\Js\Chain;

use Fohn\Ui\Js\JsRenderInterface;
use Fohn\Ui\Js\Type\Type;

class Method implements Chainable
{
    private string $name;

    /** @var JsRenderInterface[] */
    private array $arguments = [];

    final public function __construct(string $name = '', array $arguments = [])
    {
        $this->name = $name;
        // make sure each argument can be rendered via jsRender() method.
        foreach ($arguments as $arg) {
            $this->arguments[] = Type::factory($arg);
        }
    }

    public function renderChain(): string
    {
        return $this->name . static::renderArguments($this->arguments);
    }

    public static function renderMethodArguments(array $args): string
    {
        return (new static())->renderArguments($args);
    }

    public function renderArguments(array $args): string
    {
        return '(' .
               implode(',', array_map(function ($arg) {
                   return $arg->jsRender();
               }, $args)) .
               ')';
    }
}
