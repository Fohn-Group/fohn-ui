<?php

declare(strict_types=1);
/**
 * Breadcrumb.
 */

namespace Fohn\Ui\View;

use Fohn\Ui\HtmlTemplate;
use Fohn\Ui\Tailwind\Tw;

class Breadcrumb extends Lister
{
    public string $defaultTemplate = 'view/breadcrumb.html';

    /** Template regions. */
    protected const LINK_REGION = 'Link_Region';
    protected const SEPARATOR_REGION = 'Separator_Region';
    protected const NO_LINK_REGION = 'NoLink_Region';

    protected string $textColor = 'primary';

    public array $defaultTailwind = ['w-full', 'my-4', 'pb-2', 'border-b', 'border-gray-300'];

    protected function initRenderTree(): void
    {
        parent::initRenderTree();
        $this->prepareTemplate();
        $this->setItemRender();

        $this->appendTailwind(Tw::textColor($this->textColor));
    }

    /**
     * Prepare template
     *  - Split into Region.
     *  - Then set Lister Region with empty items.
     */
    protected function prepareTemplate(): void
    {
        $this->setTemplate($this->getTemplate()->cloneRegion(HtmlTemplate::TOP_TAG));

        foreach ($this->getTemplate()->getRegionTagName('_Region') as $tag) {
            $this->setRegionItems($tag, [], $this->getTemplate()->cloneRegion($tag));
            $this->getTemplate()->del($tag);
        }
    }

    /**
     * Add separator rendering if part of Link_Region.
     */
    protected function setItemRender(): void
    {
        $this->onItemRender(self::LINK_REGION, function (HtmlTemplate $template, array $items) {
            $html = $template->trySetMany($items)->renderToHtml();
            if ($items['separator']) {
                $html .= $this->getRegion(self::SEPARATOR_REGION)->regionTemplate->trySetMany($items)->renderToHtml();
            }

            return $html;
        });
    }

    public function addLink(string $label, string $url, bool $isLast = false, string $separator = '/'): self
    {
        $items = [
            'label' => $label,
            'url' => $url,
            'separator' => !$isLast ? $separator : '', ];

        $this->appendRegionItem(static::LINK_REGION, $items);

        return $this;
    }

    public function addLast(string $label): self
    {
        $this->appendRegionItem(static::NO_LINK_REGION, ['label' => $label]);

        return $this;
    }
}
