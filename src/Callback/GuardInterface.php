<?php

declare(strict_types = 1);
/**
 * Guard
 */

namespace Fohn\Ui\Callback;

interface GuardInterface
{
    /**
     * Verify request against csrf attack.
     */
    public function verifyCSRF(): void;
}
