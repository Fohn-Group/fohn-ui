<?php

declare(strict_types = 1);
/**
 * Session test.
 *
 */

namespace Fohn\Ui\Tests\Session;

use Fohn\Ui\App;
use Fohn\Ui\Service\Session;

class SessionTest extends \PHPUnit\Framework\TestCase
{

    public function testNamespace(): void
    {
        $session = new MockSession();
        $session->set('k', 'v', false);

        $this->assertTrue(isset($_SESSION[$session->namespace]));
    }

    public function testSetGet(): void
    {
        $session = new MockSession();

        $session->set('k', 'v', false);
        $this->assertSame('v', $session->get('k'));
        $this->assertSame($_SESSION[$session->namespace]['k'], 'v' );

        $this->assertSame(null, $session->get('noKey'));
        $this->assertSame('value', $session->get('noKey', 'value'));
    }

    public function testSetMultiple(): void
    {
        $session = new MockSession();
        $values = ['k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3'];
        $session->setMultiple($values);

        $this->assertSame('v2', $session->get('k2'));
        $this->assertSame($values, $session->body());
    }

    public function testForget(): void
    {
        $session = new MockSession();

        $session->set('k1', 'v1');
        $session->set('k2', 'v2');
        $session->forget('k2');
        $this->assertSame(['k1' => 'v1'], $session->body(true));
        $this->assertSame([], $_SESSION);
    }

    public function testRetrieve(): void
    {
        $session = new MockSession();

        $session->set('k', 'v');

        $this->assertSame('v', $session->retrieve('k'));
        $this->assertFalse(isset($_SESSION[$session->namespace]['k']));
    }

}

