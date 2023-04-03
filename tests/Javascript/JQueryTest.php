<?php

declare(strict_types=1);
/**
 * JQuery test.
 */

namespace Fohn\Ui\Tests\Javascript;

use Fohn\Ui\HtmlTemplate;
use Fohn\Ui\Js\Jquery;
use Fohn\Ui\View;

class JQueryTest extends \PHPUnit\Framework\TestCase
{
    private function getView(): View
    {
        return new View(['template' => new HtmlTemplate('<div id="{$id}"></div>')]);
    }

    public function testJqueryChain(): void
    {
        $view = $this->getView();
        $view->setIdAttribute('test');

        $this->assertSame('jQuery(this).html()', Jquery::withThis()->html()->jsRender());
        $this->assertSame("jQuery('.className').html()", Jquery::withSelector('.className')->html()->jsRender());
        $this->assertSame("jQuery('#test').html()", Jquery::withView($view)->html()->jsRender());
        $this->assertSame('jQuery(varName).html()', Jquery::withVar('varName')->html()->jsRender());
        $this->assertSame('jQuery.plugin()', Jquery::withSelf()->plugin()->jsRender());
    }

    public function testJqueryEvent(): void
    {
        $view = $this->getView();
        $view->setIdAttribute('test');

        Jquery::addEventTo($view, 'click')->execute(Jquery::withThis()->trigger());
        $expected = "jQuery('#test').on('click',function (e) { jQuery(this).trigger();})";

        $this->assertSame($expected, preg_replace('~\n*~', '', $view->getJavascript()));
    }

    public function testJqueryEventSelector(): void
    {
        $view = $this->getView();
        $view->setIdAttribute('test');
        Jquery::addEventTo($view, 'click', '.className')->execute(Jquery::withThis()->trigger());
        $expected = "jQuery('#test').on('click','.className',function (e) { jQuery(this).trigger();})";
        $this->assertSame($expected, preg_replace('~\n*~', '', $view->getJavascript()));
    }

    public function testJqueryPreventStop(): void
    {
        $view = $this->getView();
        $view->setIdAttribute('test');
        Jquery::addEventTo($view, 'click', null, true, true)->execute(Jquery::withThis()->trigger());
        $expected = "jQuery('#test').on('click',function (e) { e.stopPropagation(); e.preventDefault(); jQuery(this).trigger();})";
        $this->assertSame($expected, preg_replace('~\n*~', '', $view->getJavascript()));
    }
}
