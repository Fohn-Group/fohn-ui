<?php

declare(strict_types=1);

/**
 * Create a jQuery function handler.
 * Usually use with jQuery::on() method.
 * Will set function first argument to 'event'.
 */

namespace Fohn\Ui\Js;

class JqFunction extends JsFunction
{
    public const EVENT_VAR_NAME = 'e';

    public static function anonymous(array $args = []): self
    {
        return new static(array_merge([Js::var(self::EVENT_VAR_NAME)], $args));
    }

    public function preventDefault(): self
    {
        array_unshift($this->statements, Js::from(self::EVENT_VAR_NAME . '.preventDefault()'));

        return $this;
    }

    public function stopPropagation(): self
    {
        array_unshift($this->statements, Js::from(self::EVENT_VAR_NAME . '.stopPropagation()'));

        return $this;
    }
}
