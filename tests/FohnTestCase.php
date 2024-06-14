<?php

declare(strict_types=1);

namespace Fohn\Ui\Tests;

use Fohn\Ui\Tests\Concerns\MockApp;
use Fohn\Ui\Tests\Concerns\MockUi;
use PHPUnit\Framework\TestCase;

class FohnTestCase extends TestCase
{
    public function __construct(string $name = null, array $data = [], $dataName = '')
    {
        MockUi::service();
        MockUi::service()->setApp(new MockApp(['registerShutdown' => false]));

        parent::__construct($name, $data, $dataName);
    }
}
