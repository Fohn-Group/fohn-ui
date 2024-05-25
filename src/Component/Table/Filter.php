<?php

declare(strict_types=1);
/**
 * Table filter Vue Component.
 */

namespace Fohn\Ui\Component\Table;

use Fohn\Ui\Component\VueTrait;
use Fohn\Ui\Js\Js;
use Fohn\Ui\View;

class Filter extends View
{
    use VueTrait;

    public const FILTER_EVENT_TOGGLE = 'toggleFilterIcon';
    public const FILTER_PROP_ICON_NAME = 'icon-name';
    public const FILTER_PROP_ALT_ICON_NAME = 'alt-icon-name';

    public string $defaultTemplate = 'vue-component/table/table-filter.html';
    public array $defaultTailwind = [
        'mx-2',
        'content-center',
        'text-blue-500',
        'text-xl',
    ];

    protected string $iconName = 'bi bi-funnel';
    protected string $altIconName = 'bi bi-funnel-fill';

    protected function initRenderTree(): void
    {
        parent::initRenderTree();

        $this->addProperty(self::FILTER_PROP_ICON_NAME, Js::string($this->iconName));
        $this->addProperty(self::FILTER_PROP_ALT_ICON_NAME, Js::string($this->altIconName));

        $this->addEvent('click', Js::var(self::FILTER_EVENT_TOGGLE));
    }

    protected function beforeHtmlRender(): void
    {
        $this->renderEvents();
        $this->renderProperties();
        parent::beforeHtmlRender();
    }
}
