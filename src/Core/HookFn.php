<?php

declare(strict_types=1);

/**
 * Callback hook function type.
 * Execute a callback function where the callback function return type can be specified.
 *
 * For example, if the callback function should return a specific object instance, then you will define
 * the object hook accordingly.
 *
 * $view->callHook('myHook', HookFn::withTypeFn(function ($fn, $args): MyObject {
 *       return $fn(...$args);
 * }, ['arg1', 'arg2'], null);
 *
 * Using callHook method:
 *  - the first argument is the spot name to use for hook;
 *  - the second argument is the HookFn object;
 *
 * The HookFn object required three parameters:
 *  - The type function that the callback return must follow;
 *  - The arguments need to pass to the callback function when calling it;
 *  - The default value to return when the hook is called but not callback function has been declared for the hook.
 *
 * Thus, when calling onHook with the callback function, this callback function should have the proper
 * return type.
 *
 * $myObjectInstance = $view->onHook('myHook', function($arg1, $arg2) {
 *      return new MyObject($arg1, $arg2);
 * });
 *
 * Returning anything else then a MyObject instance will raise an exception.
 *
 * This class also provided shortcut handler for specific type or no return type at all.
 *  HookFn::with() // Callback can return anything.
 *  HookFn::withVoid() // Callback must not return a value.
 *  HookFn::withJsRenderInterface() // Callback must return a JsRenderInterface value.
 *  HookFn::withTws() // Callback must return a Tw instance value.
 */

namespace Fohn\Ui\Core;

use Fohn\Ui\Js\JsRenderInterface;
use Fohn\Ui\Js\JsStatements;
use Fohn\Ui\Tailwind\Tw;

class HookFn
{
    /** A Closure function with explicit return type defined. */
    private \Closure $typeFn;

    private array $fnArgs;

    /** @var mixed A default value to return when a hook is call and typeFn is not executed. */
    private $defaultValue;

    /**
     * @param mixed|null $defaultValue
     */
    final private function __construct(\Closure $typeFn, array $fnArgs = [], $defaultValue = null)
    {
        $this->typeFn = $typeFn;
        $this->fnArgs = $fnArgs;
        $this->defaultValue = $defaultValue;
    }

    /**
     * Create HookFn.
     *
     * @param mixed|null $defaultValue
     */
    protected static function makeWith(\Closure $fn, array $fnArgs = [], $defaultValue = null): self
    {
        return new static ($fn, $fnArgs, $defaultValue);
    }

    /**
     * Create HookFn by passing Closure function directly.
     *
     * @param mixed|null $defaultValue
     */
    public static function withTypeFn(\Closure $typeFn, array $fnArgs = [], $defaultValue = null): self
    {
        return static::makeWith($typeFn, $fnArgs, $defaultValue);
    }

    /**
     * HookFn where the callback function as no specific return type.
     *
     * @param mixed|null $defaultValue
     */
    public static function with(array $fnArgs = [], $defaultValue = null): self
    {
        return static::makeWith(function ($fn, $args = []) {
            return $fn(...$args);
        }, $fnArgs, $defaultValue);
    }

    /**
     *  HookFn where the callback function has no return value.
     */
    public static function withVoid(array $fnArgs = []): self
    {
        return static::makeWith(function ($fn, $args): void {
            $fn(...$args);
        }, $fnArgs);
    }

    /**
     *  HookFn where the callback function return an array.
     */
    public static function withArray(array $fnArgs = []): self
    {
        return static::makeWith(function ($fn, $args): array {
            return $fn(...$args);
        }, $fnArgs, []);
    }

    /**
     * HookFn where the callback function to be executed should return a JsRenderInterface object.
     */
    public static function withJsRenderInterface(array $fnArgs = []): self
    {
        return static::makeWith(function ($fn, $args = []): JsRenderInterface {
            return $fn(...$args);
        }, $fnArgs, JsStatements::with([]));
    }

    /**
     * HookFn where the callback function to be executed should return a Tw object.
     */
    public static function withTw(array $fnArgs = []): self
    {
        return static::makeWith(function ($fn, $args = []): Tw {
            return $fn(...$args);
        }, $fnArgs, Tw::from([]));
    }

    /**
     * @return mixed|null
     */
    public function defaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Execute this HookFn function.
     *
     * @return mixed
     */
    public function execute(\Closure $fn)
    {
        return ($this->typeFn)($fn, $this->fnArgs);
    }
}
