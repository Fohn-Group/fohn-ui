<?php

declare(strict_types=1);

namespace Fohn\Ui\Tests\Session;

use Fohn\Ui\Service\Session;

class MockSession extends Session
{
    public string $namespace = '__fohn_ui';

    public function __construct()
    {
        parent::__construct($this->namespace);
        $_SESSION = [];
    }

    protected function startSession(array $options = []): void
    {
    }
}
