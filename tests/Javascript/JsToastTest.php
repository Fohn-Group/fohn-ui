<?php

declare(strict_types=1);
/**
 * Created by abelair.
 * Date: 2023-02-27
 * Time: 10:13 a.m.
 */

namespace Fohn\Ui\Tests\Javascript;

use Fohn\Ui\Js\JsToast;
use PHPUnit\Framework\TestCase;

class JsToastTest extends TestCase
{
    public function testJsToast(): void
    {
        $expected = "fohn.toastService.notify('title','a message',{type:'success'})";
        $this->assertSame($expected, JsToast::success('title', 'a message')->jsRender());

        $expected = "fohn.toastService.notify('title','a message',{type:'error'})";
        $this->assertSame($expected, JsToast::error('title', 'a message')->jsRender());

        $expected = "fohn.toastService.notify('title','a message',{type:'warning'})";
        $this->assertSame($expected, JsToast::warning('title', 'a message')->jsRender());

        $expected = "fohn.toastService.notify('title','a message',{type:'info'})";
        $this->assertSame($expected, JsToast::info('title', 'a message')->jsRender());
    }
}
