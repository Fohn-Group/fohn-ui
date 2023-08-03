<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\Core\Utils;
use Fohn\Ui\Js\Jquery;
use Fohn\Ui\Js\JsReload;
use Fohn\Ui\Page;
use Fohn\Ui\PageLayout\Layout;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;
use Fohn\Ui\View\Button;
use Fohn\Ui\VirtualPage;

require_once __DIR__ . '/../init-ui.php';

$vp = VirtualPage::with(AppTest::createPage());

$vp2 = VirtualPage::with(AppTest::createPage());
// protect $vp2 page against csfr.
$vp2->getPage()->csfrProtect('my secret phrase', '/app-test/index.php');

$vp->onPageRequest(function ($page) use ($vp, $vp2) {
    $breadCrumb = View\Breadcrumb::addTo($page);
    $breadCrumb->addLink('Virtual Page Demo', Ui::parseRequestUrl());
    $breadCrumb->addLast('Top Virtual Page');

    View\Heading\Header::addTo($page, ['size' => 6, 'title' => 'Top Page Content']);
    View\Segment::addTo($page)->setTextContent(Utils::getLoremIpsum(12));

    $vp2->onPageRequest(function ($page) use ($vp) {
        $breadCrumb = View\Breadcrumb::addTo($page);
        $breadCrumb->addLink('Virtual Page Demo', Ui::parseRequestUrl());
        $breadCrumb->addLink('Top Virtual Page', $vp->getUrl());
        $breadCrumb->addLast('Inner Virtual Page');

        View\Heading\Header::addTo($page, ['size' => 6, 'title' => 'Inner Page Content']);
        $segment = View\Segment::addTo($page)->setTextContent(Utils::getLoremIpsum((int) 50));

        $b = Button::addTo($page, ['label' => 'Reload Loren Ipsum', 'color' => 'secondary', 'type' => 'text']);
        Jquery::addEventTo($b, 'click')->execute(JsReload::view($segment));
    });

    // button that trigger virtual page.
    $btn = View\Button::addTo($page, ['label' => 'Open Inner Virtual Page', 'type' => 'text', 'color' => 'secondary']);
    $btn->jsLinkTo($vp2->getUrl());
});

// button that trigger virtual page.
$btn = View\Button::addTo(Ui::layout(), ['label' => 'Open Virtual Page', 'color' => 'secondary', 'type' => 'text']);
$btn->jsLinkTo($vp->getUrl());

$page = Page::factory()->addLayout(Layout::factory(['template' => Ui::templateFromFile(__DIR__ . '/template/center-layout.html')]));

$vp2 = VirtualPage::with($page);
$vp2->onPageRequest(function ($page) {
    View\Heading\Header::addTo($page, ['title' => 'Center Layout', 'size' => 5])->removeTailwind('mt-6');
    View\Segment::addTo($page)->setTextContent(Utils::getLoremIpsum((int) 50));
    $btn = View\Button::addTo($page, ['label' => 'Back', 'color' => 'secondary', 'type' => 'text']);
    $btn->jsLinkTo(Ui::parseRequestUrl());
});

// button that trigger virtual page.
$btn = View\Button::addTo(Ui::layout(), ['label' => 'Display in a new layout', 'color' => 'secondary', 'type' => 'text']);
$btn->jsLinkTo($vp2->getUrl());
