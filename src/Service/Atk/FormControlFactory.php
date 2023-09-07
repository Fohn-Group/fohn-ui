<?php

declare(strict_types=1);

/**
 * Create Html Control base on an Atk Data\Model\Field.
 *
 * - determine a control factoryMethod to use depending on $field properties;
 * - set control seed using the factoryMethod;
 * - merge control seed with $field->ui['form'] property, $field->ui having priority;
 * - create control using core Factory with selected seed;
 * - execute specific control method if necessary;
 */

namespace Fohn\Ui\Service\Atk;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Fohn\Ui\Component\Form\Control;
use Fohn\Ui\Component\Form\Response\Items;
use Fohn\Ui\Core\Exception;
use Fohn\Ui\Service\Ui;

class FormControlFactory
{
    /** Factory method to use for making control. */
    protected array $ctrlFactoryMethod = [
        'input' => [__CLASS__, 'factoryInput'],
        'textarea' => [__CLASS__, 'factoryTextarea'],
        'calendar' => [__CLASS__, 'factoryCalendar'],
        'select' => [__CLASS__, 'factorySelect'],
    ];

    /** @var string[][] Initial seed for making control according to field type. */
    protected array $ctrlSeed = [
        'string' => [Control\Input::class],
        'integer' => [Control\Input::class],
        'text' => [Control\Textarea::class],
        'boolean' => [Control\Checkbox::class],
        'hidden' => [Control\Hidden::class],
        'calendar' => [Control\Calendar::class],
        'select' => [Control\Select::class],
        'float' => [Control\Number::class],
        'atk4_money' => [Control\Money::class],
    ];

    public function registerCtrlFactoryMethod(string $ctrlType, \Closure $method): self
    {
        $this->ctrlFactoryMethod[$ctrlType] = $method;

        return $this;
    }

    public function registerCtrlSeed(string $ctrlType, array $seed): self
    {
        $this->ctrlSeed[$ctrlType] = $seed;

        return $this;
    }

    /**
     * Create control using a Data\Model\Field.
     */
    public function fromField(Field $field): Control
    {
        return $this->makeControl($field);
    }

    /**
     * Create control using a Data\Model\Field seed value.
     */
    public function fromFieldSeed(string $fieldName, array $fieldSeed = []): Control
    {
        $field = (new Model())->addField($fieldName, $fieldSeed);

        return $this->makeControl($field);
    }

    /**
     * Create control using proper factory method.
     */
    protected function makeControl(Field $field): Control
    {
        $function = \Closure::fromCallable($this->ctrlFactoryMethod[$this->getFactoryMethodName($field)]);

        return $function($field);
    }

    /**
     * Return proper factory method name base on Model\Data\Field.
     */
    protected function getFactoryMethodName(Field $field): string
    {
        $name = 'input';

        if ($field->type === 'array' && $field->hasReference()) {
            $name = 'multiline';
        } elseif ($field->enum || $field->values) {
            $name = 'select';
        } elseif ($field->hasReference()) {
            $name = 'select';
        } elseif ($field->type === 'datetime' || $field->type === 'date' || $field->type === 'time') {
            $name = 'calendar';
        } elseif ($field->type === 'text') {
            $name = 'textarea';
        }

        return $name;
    }

    /**
     * Will return proper input type attribute for <input> control.
     */
    protected function getHtmlInputTypeAttribute(Field $field): string
    {
        switch ($field->type ?? 'input') {
            case 'atk4_money':
            case 'float':
            case 'integer':
            case 'number':
                return 'number';
            case 'boolean':
                return 'checkbox';
            case 'password':
                return 'password';
            default:
                return 'text';
        }
    }

    /**
     * Get control initial seed base on field type.
     */
    protected function getControlSeed(string $fieldType): array
    {
        return $this->ctrlSeed[$fieldType];
    }

    protected function factoryInput(Field $field): Control
    {
        $seed = $this->getControlSeed($field->type ?? 'string');
        $seed = array_merge($seed, [
            'inputType' => $this->getHtmlInputTypeAttribute($field),
            'caption' => $field->getCaption(),
            'isRequired' => $field->required,
            'isReadonly' => $field->readOnly,
            'controlName' => $field->shortName,
        ]);

        return $this->coreFactoryControl($this->mergeFieldSeed($field, $seed));
    }

    protected function factoryTextarea(Field $field): Control
    {
        $seed = $this->getControlSeed('text');
        $seed = array_merge($seed, [
            'caption' => $field->getCaption(),
            'isRequired' => $field->required,
            'isReadonly' => $field->readOnly,
            'controlName' => $field->shortName,
        ]);

        return $this->coreFactoryControl($this->mergeFieldSeed($field, $seed));
    }

    protected function factoryCalendar(Field $field): Control
    {
        $seed = $this->getControlSeed('calendar');
        $seed = array_merge($seed, [
            'type' => $field->type,
            'timezone' => Ui::timezone(),
            'format' => Ui::getDisplayFormat($field->type),
            'caption' => $field->getCaption(),
            'isRequired' => $field->required,
            'isReadonly' => $field->readOnly,
            'controlName' => $field->shortName,
        ]);

        return $this->coreFactoryControl($this->mergeFieldSeed($field, $seed));
    }

    private function coreFactoryControl(array $seed): Control
    {
        /** @var Control $control */
        $control = Ui::factoryFromSeed($seed);

        return $control;
    }

    /**
     * Add ui['form'] property seed if define in Data\Field.
     */
    private function mergeFieldSeed(Field $field, array $fallback): array
    {
        return Ui::service()->mergeSeeds($field->ui['form'] ?? [], $fallback);
    }

    protected function factorySelect(Field $field): Control
    {
        $seed = $this->getControlSeed('select');
        $seed = array_merge($seed, [
            'caption' => $field->getCaption(),
            'isRequired' => $field->required,
            'isReadonly' => $field->readOnly,
            'controlName' => $field->shortName,
        ]);

        /** @var Control\Select $control */
        $control = $this->coreFactoryControl($this->mergeFieldSeed($field, $seed));

        $selectableItems = [];
        if ($field->hasReference()) {
            $refModel = $field->getReference()->model;
            $refModel->setLimit($control->maxItems);

            if ($control->maxItems !== 0 && $refModel->action('count')->getOne() > $control->maxItems) {
                $control->setFilterMode(Control\Select::QUERY_MODE);
            }

            $control->onSetValue(function ($value) use ($refModel) {
                if ($value) {
                    $refEntity = $refModel->tryLoad($value);
                    if ($refEntity) {
                        $selectableItems[$value] = $refEntity->get($refEntity->titleField);
                    }
                }

                return $value;
            });

            $control->onQueryItems(function (Items $response, string $query) use ($refModel) {
                $response->setItems(self::getQueryItems($refModel, $query));
            });

            $control->onItemsRequest(function (Items $response, string $value) use ($refModel) {
                $items = self::getSelectItems($refModel);
                if ($value && !in_array($value, array_column($items, Control\Select::KEY), true)) {
                    $refEntity = $refModel->tryLoad($value);
                    if ($refEntity) {
                        $newItems = [Control\Select::KEY => $value, Control\Select::LABEL => $refEntity->get($refEntity->titleField)];
                        array_unshift($items, $newItems);
                    }
                }
                $response->setItems($items);
            });
        } else {
            if ($field->enum) {
                $selectableItems = array_combine($field->enum, $field->enum);
            } elseif ($field->values) {
                $selectableItems = $field->values;
            } else {
                throw (new Exception('Unable to set control selectables items using field'))
                    ->addMoreInfo('Field', $field);
            }
        }

        $control->setItems($selectableItems);

        return $control;
    }

    /**
     * Utility function. Export model data and format according to Select control items.
     */
    public static function getSelectItems(Model $model): array
    {
        $items = [];
        foreach ($model->export() as $item) {
            $items[] = [Control\Select::KEY => $item[$model->idField], Control\Select::LABEL => $item[$model->titleField]];
        }

        return $items;
    }

    /**
     * Utility function. Export model data that match a query string and
     * format according to Select control items.
     */
    public static function getQueryItems(Model $model, string $query): array
    {
        $items = [];
        if ($query) {
            $scope = Model\Scope::createOr();
            $scope->addCondition($model->titleField, 'like', '%' . $query . '%');
            $model->addCondition($scope);

            foreach ($model->export() as $item) {
                $items[] = [Control\Select::KEY => $item[$model->idField], Control\Select::LABEL => $item[$model->titleField]];
            }
        }

        return $items;
    }
}
