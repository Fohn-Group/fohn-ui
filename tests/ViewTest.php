<?php

declare(strict_types=1);

namespace Fohn\Ui\Tests;

use Fohn\Ui\HtmlTemplate;
use Fohn\Ui\Tests\Concerns\MockJsChain;
use Fohn\Ui\View;

class ViewTest extends FohnTestCase
{
    protected function getTopView(array $default = []): View
    {
        $this->initUiService();

        return new View($default);
    }

    public function testViewBasic(): void
    {
        $view = $this->getTopView();
        $view->setHtmlTag('a');
        $view->setViewName('anchor-view');
        $view->setTailwinds(['t1', 't2']);
        $view->appendTailwinds(['t3', 't4']);
        $view->appendTailwind('t5');
        $view->appendCssClasses('anchor-class');
        $view->appendHtmlStyles(['cursor' => 'pointer']);
        $view->setIdAttribute('anchor-id');
        $view->appendHtmlAttributes(['href' => '#']);
        $view->setTextContent('<span>Anchor</span>', false);
        $this->assertSame('<a id="anchor-id" class="t1 t2 t3 t4 t5 anchor-class" style="cursor:pointer;" href="#" data-ui-name="anchor-view"><span>Anchor</span></a>', $view->getHtml());

        $view->removeTailwind('t3');
        $view->removeCssClasses('anchor-class');
        $view->removeHtmlAttribute('href');
        $view->removeHtmlStyle('cursor');
        $this->assertSame('<a id="anchor-id" class="t1 t2 t4 t5" style="" data-ui-name="anchor-view"><span>Anchor</span></a>', $view->getHtml());
    }

    public function testViewLink(): void
    {
        $view = $this->getTopView();
        $view->linkTo('test');

        $this->assertSame('<a id="" class="" style="" href="test" target="_self" data-ui-name="view"></a>', $view->getHtml());
    }

    public function testViewActions(): void
    {
        $view = $this->getTopView();
        $view->appendJsActions([new MockJsChain('test1'), new MockJsChain('test2')]);

        $this->assertSame('test1;test2;', preg_replace('/[[:cntrl:]]/', '', $view->getJavascript()));

        $view->unshiftJsActions(new MockJsChain('test3'));

        $this->assertSame('test3;test1;test2;', preg_replace('/[[:cntrl:]]/', '', $view->getJavascript()));
    }

    public function testRenderToJson(): void
    {
        $view = $this->getTopView(['template' => new HtmlTemplate('<div></div>')]);
        $view->setIdAttribute('a-view-id');
        $view->appendJsAction(new MockJsChain('test1'));

        $viewAsArray = $view->renderToJsonArr();

        $this->assertSame($viewAsArray['jsRendered'], 'test1;');
        $this->assertSame($viewAsArray['html'], '<div></div>');
        $this->assertSame($viewAsArray['id'], 'a-view-id');
    }

    public function testInitRenderTree(): void
    {
        $uniqueViews = 10000;
        $ids = [];
        $idAttrs = [];
        $view = $this->getTopView();

        // Test for unique Id generation
        for ($i = 0; $i < $uniqueViews; ++$i) {
            $v = View::addTo($view);
            $ids[$v->getViewId()] = $v->getViewName();
            $idAttrs[$v->getIdAttribute()] = $v->getViewName();
        }

        $this->assertSame($uniqueViews, count($ids));
        $this->assertSame($uniqueViews, count($idAttrs));
        $this->assertSame($uniqueViews, count($view->getViewElements()));
    }

    public function testStickyGet(): void
    {
        $view = $this->getTopView();
        $_GET['args1'] = 'get1';
        $_GET['args2'] = 'get2';

        $viewSticky = $view->stickyGet('args1');
        $this->assertSame($viewSticky, 'get1');

        $innerView = View::addTo($view);
        $innerViewSticky = $innerView->stickyGet('args2');
        $this->assertSame($innerViewSticky, 'get2');

        $this->assertHasStickyValue($view->getUrlStickyArgs(), $_GET);
        $this->assertHasStickyValue($innerView->getUrlStickyArgs(), $_GET);

        $innerView->removeStickyGet('args2');
        $this->assertArrayNotHasKey('args2', $innerView->getUrlStickyArgs());
    }

    public function testDestroy(): void
    {
        $view = $this->getTopView();

        $innerView = View::addTo($view);
        $this->assertSame(1, count($view->getViewElements()));
        $innerView->destroy();
        $this->assertSame(0, count($view->getViewElements()));
    }

    public function testGetViewOwners(): void
    {
        $topView = $this->getTopView();
        $topView->setIdAttribute('topId');

        $ids[] = $topView->getIdAttribute();

        // start adding View to top view.
        $innerView = View::addTo($topView);
        $ids[] = $innerView->getIdAttribute();
        // Add sibling to top view.
        // This view should not be part of innerView owner.
        $innerSibling = View::addTo($topView);

        // Keep adding view to each inner View.
        for ($i = 0; $i <= 5; ++$i) {
            $innerView = View::addTo($innerView);
            if ($i < 5) {
                // collect ids except for the last one add.
                $ids[] = $innerView->getIdAttribute();
            }
        }

        // Get owners of last view added.
        $ownerIds = $this->getOwnersIds($innerView);
        $this->assertSame($ids, $ownerIds);
        // make sure $ownerIds does not contain sibling view.
        $this->assertArrayNotHasKey($innerSibling->getIdAttribute(), $ownerIds);
    }

    /**
     * Return an array of a View owners id in reverser order.
     * View::getOwners() will return an array of a view owners
     * from last children to top parent.
     */
    private function getOwnersIds(View $view): array
    {
        $ids = [];
        $owners = $view->getOwners();
        foreach ($owners as $v) {
            /** @var View $v */
            $ids[] = $v->getIdAttribute();
        }

        return array_reverse($ids);
    }

    private function assertHasStickyValue(array $stickies, array $gets): void
    {
        foreach ($stickies as $k => $v) {
            $this->assertSame($gets[$k], $v);
        }
    }
}
