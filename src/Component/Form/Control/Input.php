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

    protected array $inputAttrs = [];

    protected string $caption = '';
    protected string $hint = '';
    protected bool $isRequired = false;
    protected bool $isDisabled = false;
    protected bool $isReadonly = false;

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

    protected function initRenderTree(): void
    {
        parent::initRenderTree();
        if (!$this->inputTws) {
            $this->inputTws = Tw::from([]);
        }

        $this->inputTws = $this->inputTws->merge($this->inputDefaultTws);
    }

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function setHint(string $hint): self
    {
        $this->hint = $hint;

        return $this;
    }

    public function getHint(): ?string
    {
        return $this->hint;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function required(): self
    {
        $this->isRequired = true;

        return $this;
    }

    public function isReadonly(): bool
    {
        return $this->isReadonly;
    }

    public function readonly(): self
    {
        $this->isReadonly = true;

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->isDisabled;
    }

    public function disabled(): self
    {
        $this->isDisabled = true;

        return $this;
    }

    public function appendInputAttrs(string $attribute, ?string $value): self
    {
        $this->inputAttrs[$attribute] = $value;

        return $this;
    }

    public function setWithPostValue(?string $value): void
    {
        if ($value !== null) {
            $this->setValue($value);
        }
    }

    protected function beforeHtmlRender(): void
    {
        $this->getTemplate()->trySetJs('inputAttrs', Js::object($this->normalizeInputAttrs($this->inputAttrs)));
        $this->getTemplate()->trySet('inputTws', $this->inputTws->toString());
        $this->getTemplate()->trySetJs('hint', Js::string($this->hint));
        $this->getTemplate()->trySetJs('caption', Js::string($this->caption));
        $this->getTemplate()->trySetJs('onChanges', Type::factory($this->onChangeHandlers));
        $this->getTemplate()->trySetJs('formStoreId', Js::string($this->formStoreId));

        parent::beforeHtmlRender();
    }

    private function normalizeInputAttrs(array $inputAttrs): array
    {
        $inputAttrs['type'] = $this->inputType;
        $inputAttrs['name'] = $this->controlName;
        $inputAttrs['value'] = $this->getInputValue();

        if ($this->placeholder) {
            $inputAttrs['placeholder'] = $this->placeholder;
        }

        if ($this->isDisabled()) {
            $inputAttrs['disabled'] = true;
        }

        if ($this->isReadonly()) {
            $inputAttrs['readonly'] = true;
        }

        if ($this->isRequired()) {
            $inputAttrs['required'] = true;
        }

        return $inputAttrs;
    }
}
