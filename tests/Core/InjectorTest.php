<?php

declare(strict_types=1);
/**
 * Test InjectorTrait.
 */

namespace Fohn\Ui\Tests\Core;

use Fohn\Ui\Core\Exception;
use Fohn\Ui\Tests\Concerns\MockView;

class InjectorTest extends \PHPUnit\Framework\TestCase
{
    public function testInjectorDefault(): void
    {
        $view = new MockView();
        $this->assertSame($view->publicProps, 'aProps');

        $view = new MockView(['publicProps' => 'testa', 'protectedProps' => 'testb']);
        $this->assertSame('testa', $view->publicProps);
        $this->assertSame('testb', $view->getProtectedProps());
    }

    public function testNoPropertyException(): void
    {
        $this->expectException(Exception::class);
        $view = new MockView(['notRrealProps' => 'test']);
    }

    public function testPrivatePropertyError(): void
    {
        $this->expectException(\Error::class);
        $view = new MockView(['privateProps' => 'test']);
    }
}
