<?php

declare(strict_types=1);
/**
 * Hook containers.
 */

namespace Fohn\Ui\Core;

trait HookTrait
{
    /** @var array<string, array<int, array<\Closure>>> */
    protected array $hooks = [];

    /**
     * Call a single hookFn define by onHook.
     *
     * @return mixed|null
     */
    public function callHook(string $name, HookFn $hookFn)
    {
        if (isset($this->hooks[$name])) {
            $fx = $this->hooks[$name][0];

            return $hookFn->execute($fx[0]);
        }

        return $hookFn->defaultValue();
    }

    public function onHook(string $name, \Closure $fx): void
    {
        $this->hooks[$name][0] = [$fx];
    }

    /**
     * Call multiple hookFn using the same spot name define by onHooks.
     */
    public function callHooks(string $name, HookFn $hookFn): array
    {
        $return = [];
        if (isset($this->hooks[$name])) {
            try {
                foreach ($this->hooks[$name] as $k => $fns) {
                    foreach ($fns as $fn) {
                        $return[] = $hookFn->execute($fn);
                    }
                }
            } catch (HookBreaker $e) {
                return [$e->getReturnValue()];
            }
        }

        return $return;
    }

    /**
     * Set a multiple hook function to the same hook name or spot.
     * Each hookFn function set for the named spot will be executed by order of priority,
     * where the lowest priority being executed first.
     *
     * Hook spot name's call can be break by using breakHook during hookFn execution.
     */
    public function onHooks(string $name, \Closure $fn, int $priority = 0): void
    {
        if (!isset($this->hooks[$name][$priority])) {
            $this->hooks[$name][$priority] = [];
        }

        $this->hooks[$name][$priority][] = $fn;

        ksort($this->hooks[$name]);
    }

    /**
     * Will stop execution of current hook named spot
     * and hook will return the breakHook param value.
     *
     * @param mixed $value
     */
    public function breakHook($value): void
    {
        throw new HookBreaker($value);
    }
}
