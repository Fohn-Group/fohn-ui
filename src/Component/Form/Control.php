<?php

declare(strict_types=1);

namespace Fohn\Ui\Component\Form;

use Fohn\Ui\Component\VueInterface;
use Fohn\Ui\Component\VueTrait;
use Fohn\Ui\Js\JsFunction;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;

/**
 * Provides generic functionality for a form control.
 */
abstract class Control extends View implements VueInterface
{
    use VueTrait;

    private const COMP_NAME = 'fohn-control';
    public array $defaultTailwind = [
        'mb-4',
    ];

    /** @var mixed */
    private $value;

    public string $formStoreId = '';

    /** The name of this control. */
    protected string $controlName;
    /** An array of JsFunction to be executed on input change. */
    protected array $onChangeHandlers = [];

    protected ?\Closure $validateFx = null;
    protected ?\Closure $setValueFx = null;

    /**
     * Set control value from a POST request value.
     * If this control name is not part of the post request
     * $value will be null.
     */
    abstract public function setWithPostValue(?string $value): void;

    /**
     * @param mixed $value
     */
    public function setValue($value): self
    {
        $this->value = $this->setValueFx ? ($this->setValueFx)($value) : $value;

        return $this;
    }

    /**
     * Execute validateFx Closure function.
     */
    public function validate(): ?string
    {
        return $this->validateFx ? ($this->validateFx)($this->getValue()) : null;
    }

    public function onValidate(\Closure $fx): self
    {
        $this->validateFx = $fx;

        return $this;
    }

    public function onSetValue(\Closure $fx): self
    {
        $this->setValueFx = $fx;

        return $this;
    }

    /**
     * Hold the real input control value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Return value for the input html value attribute.
     *
     * @return mixed
     */
    public function getInputValue()
    {
        return $this->getValue();
    }

    /**
     * Add onChange handler to input control.
     * a debounced time value, in ms, may be set prior to running the onChangeHandler.
     */
    public function onChange(JsFunction $function, int $debounceValue = 0): self
    {
        $this->onChangeHandlers[] = ['fn' => $function, 'debounceValue' => $debounceValue];

        return $this;
    }

    public function getControlName(): string
    {
        return $this->controlName;
    }

    protected function beforeHtmlRender(): void
    {
        $this->createVueApp(self::COMP_NAME, [], $this->getDefaultSelector());

        parent::beforeHtmlRender();
    }
}
