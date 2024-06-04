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
        $this->addProperty('columns', Js::array([
            ['id' => 'id', 'label' => 'Id', 'componentName' => 'input', 'operatorType' => 'number', 'props' => ['type' => 'number', 'name' => 'id']],
            ['id' => 'name', 'label' => 'Name', 'componentName' => 'input', 'operatorType' => 'text', 'props' => ['type' => 'text', 'name' => 'name']],
            ['id' => 'date', 'label' => 'Date', 'componentName' => 'flat-pickr', 'operatorType' => 'date', 'props' => ['config' => ['format' => 'Y-m-d']]],
        ]));
        $this->addProperty('operators', Js::array([
            ['id' => '=', 'label' => '=', 'types' => ['number', 'date']],
            ['id' => '>', 'label' => '>', 'types' => ['number', 'date']],
            ['id' => 'contains', 'label' => 'contains', 'types' => ['text']],
            ['id' => 'notContains', 'label' => 'not contains', 'types' => ['text']],
            ['id' => 'startsWith', 'label' => 'starts with', 'types' => ['text']],
            ['id' => 'endsWith', 'label' => 'end with', 'types' => ['text']],
            ['id' => 'equals', 'label' => 'equals', 'types' => ['text']],
            ['id' => 'isEmpty', 'label' => 'is empty', 'types' => ['text', 'number']],
            ['id' => 'isNotEmpty', 'label' => 'is not empty', 'types' => ['text', 'number']],
            ['id' => 'isAnyOf', 'label' => 'is any of', 'types' => ['text', 'number']],
        ]));

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
