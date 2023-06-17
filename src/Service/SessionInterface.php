<?php

declare(strict_types=1);
/**
 * Session Interface.
 * @method getInstance()
 */

namespace Fohn\Ui\Service;

interface SessionInterface
{
    /**
     * Set session option.
     */
    public function setOptions(array $options): void;

    public function set(string $key, string $value, bool $keepOpen = false): void;

    public function setMultiple(array $values, bool $keepOpen = false): void;

    public function retrieve(string $key, string $default = null): ?string;

    public function forget(string $key = null): void;

    public function get(string $key, string $default = null): ?string;

    public function body(bool $clear = false): array;

    public function destroy(): void;
}
