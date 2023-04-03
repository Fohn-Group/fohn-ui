<?php

declare(strict_types=1);

namespace Fohn\Ui\Component\Form\Control;

use Fohn\Ui\Component\Form\Control;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\Type\Type;
use Fohn\Ui\Tailwind\Tw;

/**
 * Input element.
 */
class Input extends Control
{
    public string $defaultTemplate = 'vue-component/form/control/input.html';
    public string $inputType = 'text';
    public string $placeholder = '';

    public ?Tw $inputTws = null;
    public array $inputDefaultTws = [
        'mt-1',
        'block',
        'w-full',
        'rounded-md',
        'border-gray-300',
        'shadow-sm',
        'focus:border-blue-300',
        'focus:ring-0',
        'focus:ring-blue-200',
        'focus:ring-opacity-50',
    ];

    public function setWithPostValue(?string $value): void
    {
        if ($value !== null) {
            $this->setValue($value);
        }
    }

    protected function initRenderTree(): void
    {
        parent::initRenderTree();
        if (!$this->inputTws) {
            $this->inputTws = Tw::from([]);
        }

        $this->inputTws = $this->inputTws->merge($this->inputDefaultTws);
    }

    protected function beforeHtmlRender(): void
    {
        $this->getTemplate()->trySet('inputTws', $this->inputTws->toString());
        $this->getTemplate()->trySetJs('name', Js::string($this->controlName));
        $this->getTemplate()->trySetJs('value', Type::factory($this->getInputValue()));
        $this->getTemplate()->trySetJs('hint', Js::string($this->hint));
        $this->getTemplate()->trySetJs('caption', Js::string($this->caption));
        $this->getTemplate()->trySetJs('isRequired', Js::boolean($this->isRequired()));
        $this->getTemplate()->trySetJs('isReadOnly', Js::boolean($this->isReadonly()));
        $this->getTemplate()->trySetJs('isDisabled', Js::boolean($this->isDisabled()));
        $this->getTemplate()->trySetJs('onChanges', Type::factory($this->onChangeHandlers));

        $this->getTemplate()->trySetJs('type', Js::string($this->inputType));
        $this->getTemplate()->trySetJs('placeholder', Js::string($this->placeholder));
        $this->getTemplate()->trySetJs('formStoreId', Js::string($this->formStoreId));

        parent::beforeHtmlRender();
    }
}
