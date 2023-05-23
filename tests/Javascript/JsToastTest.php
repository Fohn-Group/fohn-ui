<?php

declare(strict_types=1);
/**
 * JsToast.
 */

namespace Fohn\Ui\Tests\Javascript;

use Fohn\Ui\Js\JsToast;
use PHPUnit\Framework\TestCase;

class JsToastTest extends TestCase
{
    public function testJsToast(): void
    {
        $expected = "fohn.toastService.notify('title','a message',{},false)";
        $this->assertSame($expected, JsToast::notify('title', 'a message', [], false)->jsRender());

        $expected = "fohn.toastService.notify('title','a message',{a:'v'},true)";
        $this->assertSame($expected, JsToast::notify('title', 'a message', ['a' => 'v'])->jsRender());

        $expected = "fohn.toastService.notify('title','a message',{type:'success'},true)";
        $this->assertSame($expected, JsToast::success('title', 'a message')->jsRender());

        $expected = "fohn.toastService.notify('title','a message',{a:'v',type:'success'},true)";
        $this->assertSame($expected, JsToast::success('title', 'a message', ['a' => 'v'])->jsRender());

        $expected = "fohn.toastService.notify('title','a message',{type:'success'},false)";
        $this->assertSame($expected, JsToast::success('title', 'a message', [], false)->jsRender());

        $expected = "fohn.toastService.notify('title','a message',{type:'error'},true)";
        $this->assertSame($expected, JsToast::error('title', 'a message')->jsRender());

        $expected = "fohn.toastService.notify('title','a message',{type:'warning'},true)";
        $this->assertSame($expected, JsToast::warning('title', 'a message')->jsRender());

        $expected = "fohn.toastService.notify('title','a message',{type:'info'},true)";
        $this->assertSame($expected, JsToast::info('title', 'a message')->jsRender());
    }
}
