<?php

declare(strict_types=1);

namespace Fohn\Ui\PageLayout;

use Fohn\Ui\Component\Navigation;
use Fohn\Ui\View;

/**
 * Implements a classic 100% width admin layout.
 *
 * This admin layout use Navigation Vue component for page access.
 */
class SideNavigation extends Layout
{
    public string $defaultTemplate = 'layout/admin.html';
    public string $topBarTitle = '';
    public string $navigationWidth = '52';
    public string $navigationBreakPoint = 'lg';
    public View $topBarContent;
    public View $burger;
    public string $title = '';
    public Navigation $navigation;

    protected function initRenderTree(): void
    {
        parent::initRenderTree();
        $this->initAdmin();
    }

    protected function initAdmin(): void
    {
        $this->topBarContent = View::addTo($this, ['defaultTailwind' => ['mx-2']], 'topBarContent');

        $this->navigation = Navigation::addTo(
            $this,
            [
                'title' => $this->topBarTitle,
                'width' => $this->navigationWidth,
                'breakPoint' => $this->navigationBreakPoint,
            ],
            'navigation'
        );
    }

    /**
     * Add a group to left menu.
     */
    public function addNavigationGroup(Navigation\Group $group): self
    {
        $this->navigation->addGroup($group);

        return $this;
    }

    protected function beforeHtmlRender(): void
    {
        // @var self $view
        $this->getTemplate()->trySet('breakPoint', $this->navigationBreakPoint);
        $this->getTemplate()->trySet('width', $this->navigationWidth);
        parent::beforeHtmlRender();
    }
}
