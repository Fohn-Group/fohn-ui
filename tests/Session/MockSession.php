<?php

declare(strict_types = 1);

namespace Fohn\Ui\Tests\Session;

use Fohn\Ui\Core\Exception;
use Fohn\Ui\Service\Session;

class MockSession extends Session
{

    public string $namespace = '__fohn_ui';
    public function __construct()
    {
        $_SESSION = [];
    }

    protected function startSession(array $options = []): void
    {
    }

}
