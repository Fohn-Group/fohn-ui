<?php

declare(strict_types=1);

namespace Fohn\Ui\Component\Form\Control;

use Fohn\Ui\Component\Form\Control;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\Type\Type;
use Fohn\Ui\Service\Ui;
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

    protected bool $sanitizePostValue = true;

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
        if ($this->placeholder) {
            $this->appendInputHtmlAttribute('placeholder', $this->placeholder);
        }

        if ($this->isRequired) {
            $this->required();
        }

        if ($this->isReadonly) {
            $this->readonly();
        }

        if ($this->isDisabled) {
            $this->disabled();
        }

        $this->inputTws = $this->inputTws->merge($this->inputDefaultTws);
    }

    public function sanitize(?string $value): ?string
    {
        return ($value && $this->sanitizePostValue) ? Ui::service()->sanitize($value) : $value;
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
        return isset($this->inputAttrs['required']) && $this->inputAttrs['required'] === 'true';
    }

    public function required(): self
    {
        $this->appendInputHtmlAttribute('required', 'true');

        return $this;
    }

    public function isReadonly(): bool
    {
        return isset($this->inputAttrs['readonly']) && $this->inputAttrs['readonly'] === 'true';
    }

    public function readonly(): self
    {
        $this->appendInputHtmlAttribute('readonly', 'true');

        return $this;
    }

    public function isDisabled(): bool
    {
        return isset($this->inputAttrs['disabled']) && $this->inputAttrs['disabled'] === 'true';
    }

    public function disabled(): self
    {
        $this->appendInputHtmlAttribute('disabled', 'true');

        return $this;
    }

    public function appendInputHtmlAttribute(string $attributeName, ?string $value): self
    {
        $this->inputAttrs[$attributeName] = $value;

        return $this;
    }

    public function removeInputHtmlAttribute(string $attributeName): self
    {
        if (isset($this->inputAttrs[$attributeName])) {
            unset($this->inputAttrs[$attributeName]);
        }

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
        $this->getTemplate()->trySetJs('hint', Js::string($this->hint));
        $this->getTemplate()->trySetJs('caption', Js::string($this->caption));
        $this->getTemplate()->trySetJs('onChanges', Type::factory($this->onChangeHandlers));
        $this->getTemplate()->setJs('formStoreId', Js::string($this->formStoreId));
        $this->getTemplate()->trySet('inputTws', $this->inputTws->toString());

        parent::beforeHtmlRender();
    }

    private function normalizeInputAttrs(array $inputAttrs): array
    {
        $inputAttrs['type'] = $this->inputType;
        $inputAttrs['name'] = $this->controlName;
        $inputAttrs['value'] = $this->getInputValue();

        return $inputAttrs;
    }
}
