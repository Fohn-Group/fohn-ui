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

    private const COMP_NAME = 'fohn-table-filter';

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
        $this->addProperty('columns', Js::array([['value' => 'id', 'label' => 'Id', 'type' => 'number'], ['value' => 'name', 'label' => 'Name', 'type' => 'text']]));
        $this->addProperty('operators', Js::array([
            ['value' => '>', 'label' => '>', 'type' => ['number']],
            ['value' => '=', 'label' => '=', 'type' => ['number', 'text']],
            ['value' => 'contains', 'label' => 'Contains', 'type' => ['text']],
            ]
        ));

        $this->addEvent('click', Js::var(self::FILTER_EVENT_TOGGLE));
    }

    protected function beforeHtmlRender(): void
    {
        $this->renderEvents();
        $this->renderProperties();

        $this->createVueApp(self::COMP_NAME, [], $this->getDefaultSelector());

        parent::beforeHtmlRender();
    }
}
