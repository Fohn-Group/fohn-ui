<?php

declare(strict_types=1);
/**
 * Table filter Vue Component.
 */

namespace Fohn\Ui\Component\Table;

use Fohn\Ui\Component\Table\Filter\FilterInterface;
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

    /** @var array<string, FilterInterface> */
    protected array $filters = [];

    protected array $matchTypes = [
        ['id' => 'and', 'label' => 'And'],
        ['id' => 'or', 'label' => 'Or'],
    ];

    protected array $operators = [
        ['id' => 'contains', 'label' => 'Contains', 'types' => ['text'], 'requiredValue' => true],
        ['id' => 'notContains', 'label' => 'Not Contains', 'types' => ['text'], 'requiredValue' => true],
        ['id' => 'startsWith', 'label' => 'Starts With', 'types' => ['text'], 'requiredValue' => true],
        ['id' => 'endsWith', 'label' => 'End With', 'types' => ['text'], 'requiredValue' => true],
        ['id' => 'equals', 'label' => 'Equals', 'types' => ['text'], 'requiredValue' => true],
        ['id' => '=', 'label' => '=', 'types' => ['number'], 'requiredValue' => true],
        ['id' => '!=', 'label' => '!=', 'types' => ['number'], 'requiredValue' => true],
        ['id' => '>', 'label' => '>', 'types' => ['number'], 'requiredValue' => true],
        ['id' => '>=', 'label' => '>=', 'types' => ['number'], 'requiredValue' => true],
        ['id' => '<', 'label' => '<', 'types' => ['number'], 'requiredValue' => true],
        ['id' => '<=', 'label' => '<=', 'types' => ['number'], 'requiredValue' => true],
        ['id' => 'is', 'label' => 'Is', 'types' => ['date', 'datetime', 'time', 'boolean'], 'requiredValue' => true],
        ['id' => 'isNot', 'label' => 'Is Not', 'types' => ['date', 'datetime', 'time'], 'requiredValue' => true],
        ['id' => 'isAfter', 'label' => 'Is After', 'types' => ['date', 'datetime', 'time'], 'requiredValue' => true],
        ['id' => 'isOnOrAfter', 'label' => 'Is On Or After', 'types' => ['date', 'datetime', 'time'], 'requiredValue' => true],
        ['id' => 'isBefore', 'label' => 'Is Before', 'types' => ['date', 'datetime', 'time'], 'requiredValue' => true],
        ['id' => 'isOnOrBefore', 'label' => 'Is On Or Before', 'types' => ['date', 'datetime', 'time'], 'requiredValue' => true],
        ['id' => 'isEmpty', 'label' => 'Is Empty', 'types' => ['text', 'number', 'date', 'datetime', 'time'], 'requiredValue' => false],
        ['id' => 'isNotEmpty', 'label' => 'Is Not Empty', 'types' => ['text', 'number', 'date', 'datetime', 'time'], 'requiredValue' => false],
        //        ['id' => 'isAnyOf', 'label' => 'Is Any Of', 'types' => ['text', 'number'], 'requiredValue' => true], To add with multiple value component
    ];

    public static function getComponentName(string $type): string
    {
        return static::VUE_COMPONENT_NAME_TYPE[$type];
    }

    public function addFilter(FilterInterface $filter): FilterInterface
    {
        $this->filters[$filter->getId()] = $filter;

        return $filter;
    }

    /**
     * Provide an array of operator [id => label] for translation.
     * Ex: setOperatorsLabel(['contains' => 'Contient']).
     */
    public function setOperatorsLabels(array $operators): self
    {
        foreach ($operators as $id => $label) {
            // find operators Id in list and set label
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
        foreach ($this->filters as $filter) {
            $columns[] = $filter->getDefinition();
        }

        return $columns;
    }
}
