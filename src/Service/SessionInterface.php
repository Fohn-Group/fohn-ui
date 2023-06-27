<?php

declare(strict_types=1);
/**
 * Session Interface.
 */

namespace Fohn\Ui\Service;

interface SessionInterface
{
    /**
     * Set session option.
     */
    public function setOptions(array $options): void;

    public function get(string $key, string $default = null): ?string;

    public function set(string $key, string $value, bool $keepOpen = false): void;

    public function setMultiple(array $values, bool $keepOpen = false): void;

    /**
     * Retrieve a key and remove it from body.
     */
    public function retrieve(string $key, string $default = null): ?string;

    /**
     * Remove a key from body.
     */
    public function forget(string $key = null): void;

    /**
     * Retrieve all key from Session.
     */
    public function body(bool $clear = false): array;

    public function destroy(): void;

    public function regenerate(bool $clear = false): bool;
}
