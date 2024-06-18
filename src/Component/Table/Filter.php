<?php

declare(strict_types=1);
/**
 * Table filter Vue Component.
 */

namespace Fohn\Ui\Component\Table;

use Fohn\Ui\Component\Table\Filter\FilterColumnInterface;
use Fohn\Ui\Component\Table\Filter\FilterOperators;
use Fohn\Ui\Component\VueTrait;
use Fohn\Ui\Js\Js;
use Fohn\Ui\View;

class Filter extends View implements FilterInterface
{
    use VueTrait;

    private const COMP_NAME = 'fohn-table-filter';

    public const FILTER_EVENT_TOGGLE = 'toggleFilterIcon';
    public const FILTER_PROP_ICON_NAME = 'icon-name';
    public const FILTER_PROP_ALT_ICON_NAME = 'alt-icon-name';

    protected const INPUT_VUE_COMPONENT = 'input';
    protected const DATE_VUE_COMPONENT = 'flat-pickr';

    protected array $removeAllBtnSeed = [View\Button::class, 'label' => 'Remove All', 'type' => 'text'];

    /**
     * Vue component to use according to data type.
     */
    protected const VUE_COMPONENT_NAME_TYPE = [
        'text' => self::INPUT_VUE_COMPONENT,
        'number' => self::INPUT_VUE_COMPONENT,
        'date' => self::DATE_VUE_COMPONENT,
        'datetime' => self::DATE_VUE_COMPONENT,
        'time' => self::DATE_VUE_COMPONENT,
    ];

    public string $defaultTemplate = 'vue-component/table/table-filter.html';
    public array $defaultTailwind = [
        'mx-2',
        'content-center',
        'text-blue-500',
        'text-xl',
    ];

    protected string $iconName = 'bi bi-funnel';
    protected string $altIconName = 'bi bi-funnel-fill';

    /** @var array<string, FilterColumnInterface> */
    protected array $columnFilters = [];

    protected array $matchTypes = [
        ['id' => 'and', 'label' => 'And'],
        ['id' => 'or', 'label' => 'Or'],
    ];

    protected array $operators = [
        ['id' => FilterOperators::TEXT_CONTAINS, 'label' => 'Contains', 'types' => ['text'], 'requiredValue' => true],
        ['id' => FilterOperators::TEXT_NOT_CONTAINS, 'label' => 'Not Contains', 'types' => ['text'], 'requiredValue' => true],
        ['id' => FilterOperators::TEXT_STARTS_WITH, 'label' => 'Starts With', 'types' => ['text'], 'requiredValue' => true],
        ['id' => FilterOperators::TEXT_NOT_STARTS_WITH, 'label' => 'Not Starts With', 'types' => ['text'], 'requiredValue' => true],
        ['id' => FilterOperators::TEXT_ENDS_WITH, 'label' => 'End With', 'types' => ['text'], 'requiredValue' => true],
        ['id' => FilterOperators::TEXT_NOT_ENDS_WITH, 'label' => 'Not End With', 'types' => ['text'], 'requiredValue' => true],
        ['id' => FilterOperators::TEXT_EQUALS, 'label' => 'Equals', 'types' => ['text'], 'requiredValue' => true],
        ['id' => FilterOperators::EQUAL, 'label' => '=', 'types' => ['number'], 'requiredValue' => true],
        ['id' => FilterOperators::NOT_EQUAL, 'label' => '!=', 'types' => ['number'], 'requiredValue' => true],
        ['id' => FilterOperators::GREATER, 'label' => '>', 'types' => ['number'], 'requiredValue' => true],
        ['id' => FilterOperators::GREATER_EQUAL, 'label' => '>=', 'types' => ['number'], 'requiredValue' => true],
        ['id' => FilterOperators::LESS, 'label' => '<', 'types' => ['number'], 'requiredValue' => true],
        ['id' => FilterOperators::LESS_EQUAL, 'label' => '<=', 'types' => ['number'], 'requiredValue' => true],
        ['id' => FilterOperators::IS, 'label' => 'Is', 'types' => ['date', 'datetime', 'time', 'boolean'], 'requiredValue' => true],
        ['id' => FilterOperators::IS_NOT, 'label' => 'Is Not', 'types' => ['date', 'datetime', 'time'], 'requiredValue' => true],
        ['id' => FilterOperators::IS_AFTER, 'label' => 'Is After', 'types' => ['date', 'datetime', 'time'], 'requiredValue' => true],
        ['id' => FilterOperators::IS_ON_OR_AFTER, 'label' => 'Is On Or After', 'types' => ['date', 'datetime', 'time'], 'requiredValue' => true],
        ['id' => FilterOperators::IS_BEFORE, 'label' => 'Is Before', 'types' => ['date', 'datetime', 'time'], 'requiredValue' => true],
        ['id' => FilterOperators::IS_ON_OR_BEFORE, 'label' => 'Is On Or Before', 'types' => ['date', 'datetime', 'time'], 'requiredValue' => true],
        ['id' => FilterOperators::IS_EMPTY, 'label' => 'Is Empty', 'types' => ['text', 'number', 'date', 'datetime', 'time'], 'requiredValue' => false],
        ['id' => FilterOperators::IS_NOT_EMPTY, 'label' => 'Is Not Empty', 'types' => ['text', 'number', 'date', 'datetime', 'time'], 'requiredValue' => false],
        // ['id' => FilterOperators::IS_ANY_OF, 'label' => 'Is Any Of', 'types' => ['text', 'number'], 'requiredValue' => true], To add with multiple value component
    ];

    public static function getComponentName(string $type): string
    {
        return static::VUE_COMPONENT_NAME_TYPE[$type];
    }

    public function addColumnFilter(FilterColumnInterface $columnFilter): FilterColumnInterface
    {
        $this->columnFilters[$columnFilter->getId()] = $columnFilter;

        return $columnFilter;
    }

    public function getColumnFilter(string $columnFilterId): FilterColumnInterface
    {
        return $this->columnFilters[$columnFilterId];
    }

    /**
     * Provide an array of operator [id => label] for translation.
     * Ex: setOperatorsLabel(['contains' => 'Contient']).
     */
    public function setOperatorsLabels(array $operators): self
    {
        foreach ($operators as $id => $label) {
            // find operators id in list and set label
            for ($i = 0; $i < count($this->operators); ++$i) {
                if ($this->operators[$i]['id'] === $id) {
                    $this->operators[$i]['label'] = $label;
                }
            }
        }

        return $this;
    }

    protected function beforeHtmlRender(): void
    {
        $btn = $this->addView(View\Button::factoryFromSeed($this->removeAllBtnSeed), 'removeBtn');
        $this->bindVueEvent($btn, 'click', 'removeAll');

        $this->addProperty('columns', Js::array($this->getFitlersColumn()));
        $this->addProperty('operators', Js::array($this->operators));
        $this->addProperty('match-types', Js::array($this->matchTypes));
        $this->addProperty(self::FILTER_PROP_ICON_NAME, Js::string($this->iconName));
        $this->addProperty(self::FILTER_PROP_ALT_ICON_NAME, Js::string($this->altIconName));

        $this->renderEvents();
        $this->renderProperties();

        $this->createVueApp(self::COMP_NAME, [], $this->getDefaultSelector());

        parent::beforeHtmlRender();
    }

    private function getFitlersColumn(): array
    {
        $columns = [];
        foreach ($this->columnFilters as $filter) {
            $columns[] = $filter->getDefinition();
        }

        return $columns;
    }
}
