<?php

declare(strict_types=1);
/**
 * Section Header.
 */

namespace Fohn\Ui\View\Heading;

use Fohn\Ui\View;
use Fohn\Ui\View\Icon;
use Fohn\Ui\View\Image;

class SectionHeader extends View
{
    public string $defaultTemplate = 'view/heading/section-header.html';
    public int $headerSize = 1;
    public string $iconSize = '3x';
    public string $iconName = '';
    public string $imageSrc = '';
    public ?string $title = null;
    public ?string $subTitle = null;
    protected Header $headerView;
    protected View $leftContentView;
    protected View $contentView;

    protected function initRenderTree(): void
    {
        parent::initRenderTree();

        // contentView hold Header and sub title view.
        $this->contentView = View::addTo($this)->appendCssClasses('section-content');
        // left content hold icon or image.
        $this->leftContentView = View::addTo($this, [], 'leftContent')->appendCssClasses('section-left-content contents');

        $title = $this->title ?: $this->htmlContent ?: null;
        if ($title) {
            $this->addHeader($title, $this->headerSize);
        }

        $this->htmlContent = null;

        if ($this->subTitle) {
            $this->addSubTitle($this->subTitle);
        }

        if ($this->imageSrc) {
            $this->addImage($this->imageSrc);
        } elseif ($this->iconName) {
            $this->addIcon($this->iconName);
        }

        $this->appendTailwinds(
            ['mb-6', 'mt-2']
        );
    }

    public function getHeaderView(): Header
    {
        return $this->headerView;
    }

    public function addImage(string $imageSrc): self
    {
        Image::addTo($this->leftContentView, ['src' => $this->imageSrc])
            ->appendTailwinds([
                'w-10',
                'object-contain',
            ]);

        return $this;
    }

    public function addIcon(string $iconName): self
    {
        Icon::addTo($this->leftContentView, ['iconName' => $this->iconName, 'size' => $this->iconSize])
            ->appendTailwinds([
                'w-10',
                'object-contain',
            ]);

        return $this;
    }

    public function addHeader(string $title, int $size = 1, bool $withMargin = false): self
    {
        $this->headerView = Header::addTo($this->contentView, ['title' => $title, 'size' => $size, 'hasMargin' => $withMargin]);

        return $this;
    }

    public function alignCenter(string $variant = ''): self
    {
        $this->appendTailwind('place-content-center');

        return $this;
    }

    public function addSubTitle(string $subTitle, string $textColor = 'text-slate-600'): View
    {
        return View::addTo($this->contentView, ['htmlContent' => $subTitle])->appendTailwind($textColor);
    }

    public function setHeaderSize(int $size, string $variant = ''): self
    {
        $this->getHeaderView()->setSize($size, $variant);

        return $this;
    }

    protected function beforeHtmlRender(): void
    {
        if (count($this->leftContentView->getViewElements()) > 0) {
            $this->contentView->appendTailwind('ml-4');
        }
        parent::beforeHtmlRender();
    }
}
