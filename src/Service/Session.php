<?php

declare(strict_types=1);
/**
 * Session Management.
 */

namespace Fohn\Ui\Service;

use Fohn\Ui\Core\Exception;

class Session implements SessionInterface
{
    protected const READ_ONLY_OPTION = 'read_and_close';

    protected static ?SessionInterface $instance = null;

    private array $sessionOptions = [];
    private string $namespace = '__fohn_ui';

    public function setOptions(array $options): void
    {
        $this->sessionOptions = $options;
    }

    public function set(string $key, string $value, bool $keepOpen = false): void
    {
        $this->startSession(array_merge($this->sessionOptions, [self::READ_ONLY_OPTION => false]));

        if (!isset($_SESSION[$this->namespace])) {
            $_SESSION[$this->namespace] = [];
        }

        $_SESSION[$this->namespace][$key] = $value;

        if (!$keepOpen) {
            $this->closeSession();
        }
    }

    public function setMultiple(array $values, bool $keepOpen = false): void
    {
        foreach ($values as $k => $value) {
            $this->set($k, $value, true);
        }

        if (!$keepOpen) {
            $this->closeSession();
        }
    }

    /**
     * Retrieve a Session key and remove it after.
     * Will return $default if key is not set.
     */
    public function retrieve(string $key, string $default = null): ?string
    {
        $value = $this->get($key, $default);
        $this->forget($key);

        return $value;
    }

    public function forget(string $key = null): void
    {
        $this->startSession();
        if (!$key && isset($_SESSION[$this->namespace])) {
            unset($_SESSION[$this->namespace]);
        }
        if ($key && isset($_SESSION[$this->namespace]) && isset($_SESSION[$this->namespace][$key])) {
            unset($_SESSION[$this->namespace][$key]);
        }
    }

    public function get(string $key, string $default = null): ?string
    {
        $this->startSession(array_merge($this->sessionOptions, [self::READ_ONLY_OPTION => true]));

        if (!isset($_SESSION[$this->namespace])) {
            return $default;
        } elseif (!isset($_SESSION[$this->namespace][$key])) {
            return $default;
        }

        return $_SESSION[$this->namespace][$key];
    }

    /**
     * Get all session keys set for this namespace.
     */
    public function body(bool $clear = false): array
    {
        $body = [];
        $this->startSession(array_merge($this->sessionOptions, [self::READ_ONLY_OPTION => true]));

        if (!isset($_SESSION[$this->namespace])) {
            return $body;
        }

        foreach ($_SESSION[$this->namespace] as $k => $value) {
            $body[$k] = $value;
        }

        if ($clear) {
            unset($_SESSION[$this->namespace]);
        }

        return $body;
    }

    public function regenerate(bool $clear = false): bool
    {
        $this->startSession($this->sessionOptions);

        return session_regenerate_id($clear);
    }

    public function destroy(): void
    {
        $this->startSession([]);
        $_SESSION = [];
        $cookieParam = session_get_cookie_params();
        setcookie(session_name(), '', time() - 86400, $cookieParam['path'], $cookieParam['domain'], $cookieParam['secure'], $cookieParam['httponly']);
        session_destroy();
    }

    protected function startSession(array $options = []): void
    {
        if (!$this->hasStatus(\PHP_SESSION_ACTIVE)) {
            $status = session_start($options);

            if (!$status) {
                throw new Exception('Unable to start session.');
            }
        }
    }

    protected function closeSession(): void
    {
        if ($this->hasStatus(PHP_SESSION_ACTIVE)) {
            $status = session_write_close();

            if (!$status) {
                throw new Exception('Unable to close session.');
            }
        }

    }

    private function hasStatus(int $status): bool
    {
        return session_status() === $status;
    }
}
