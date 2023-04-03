<?php

declare(strict_types=1);

namespace Fohn\Ui\Tests;

use Fohn\Ui\HtmlTemplate;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsFunction;
use Fohn\Ui\Js\Type\Variable;
use Fohn\Ui\View;
use Fohn\Ui\ViewRenderer;

class ViewRendererTest extends \PHPUnit\Framework\TestCase
{
    public function testViewRenderedTemplate(): void
    {
        $view = new View(['template' => new HtmlTemplate('<div>{$Content}</div>')]);
        $renderer = new ViewRenderer($view);

        $this->assertSame('<div></div>', $renderer->getHtml());

        $view->addView(new View(['template' => new HtmlTemplate('<div>inner div</div>')]), 'Content');
        $renderer = new ViewRenderer($view);
        $this->assertSame('<div><div>inner div</div></div>', $renderer->getHtml());
        $this->assertSame('<div>inner div</div>', $renderer->getHtml('Content'));
    }

    public function testViewRenderedJsActions(): void
    {
        $view = new View(['template' => new HtmlTemplate('<div>{$Content}</div>')]);
        $view->appendJsAction(JsFunction::arrow([Variable::set('varName')])->execute(Js::from('console.log(varName)')));

        $renderer = new ViewRenderer($view);
        $this->assertSame('(varName) => { console.log(varName);}', $renderer->getJavascript());

        $innerView = new View(['template' => new HtmlTemplate('<div>inner div</div>')]);
        $innerView->appendJsAction(JsFunction::arrow([Variable::set('innerVarName')])->execute(Js::from('console.log(innerVarName)')));
        $view->addView($innerView, 'Content');
        $renderer = new ViewRenderer($view);
        $this->assertSame('(varName) => { console.log(varName);};(innerVarName) => { console.log(innerVarName);}', $renderer->getJavascript());
    }
}
