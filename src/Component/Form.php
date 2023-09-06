<?php

declare(strict_types=1);

namespace Fohn\Ui\Component;

use Fohn\Ui\Callback\Ajax;
use Fohn\Ui\Callback\Data;
use Fohn\Ui\Component\Form\Control;
use Fohn\Ui\Component\Form\Layout\FormLayoutInterface;
use Fohn\Ui\Core\Exception;
use Fohn\Ui\Core\HookFn;
use Fohn\Ui\Core\HookTrait;
use Fohn\Ui\Js\JsRenderInterface;
use Fohn\Ui\Js\Type\Type;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;
use Fohn\Ui\View\Button;

class Form extends View implements VueInterface
{
    use HookTrait;
    use VueTrait;

    private const COMP_NAME = 'fohn-form';
    private const PINIA_PREFIX = '__form_';
    protected const MAIN_LAYOUT = 'default';
    protected const FORM_ID_SUFFIX = '_form';

    public const HOOK_SUBMIT = self::class . '@submit';
    public const HOOK_BEFORE_CONTROL_ADD = self::class . '@beforeControlAdd';
    public const HOOKS_GET_VALUES = self::class . '@onGetValues';

    public string $defaultTemplate = 'vue-component/form.html';

    /** Handling form submission. */
    protected Ajax $submitCb;
    /** Handling Form control value. */
    protected ?Data $valuesCb = null;
    public ?string $dataRecordId = null;

    public bool $canLeave = true;
    public string $loadingMsg = '';

    /**
     * A current layout of a form, needed if you call $form->addControl().
     *
     * @var FormLayoutInterface[]
     */
    protected array $layouts = [];

    public ?FormLayoutInterface $defaultLayout = null;

    /** @var Control[] of Form\Control objects */
    private array $controls = [];

    /** Store [inputName => [errorMessages]] array. Form control will display error message when form is submitted. */
    private array $validationErrors = [];

    protected function initRenderTree(): void
    {
        parent::initRenderTree();

        $this->initCallback();
        $this->initDefaultLayout(self::MAIN_LAYOUT, $this->defaultLayout);
    }

    protected function initDefaultLayout(string $layoutName, FormLayoutInterface $layout = null): self
    {
        $this->defaultLayout = $layout ?: Ui::service()->getFormLayout();

        $this->addLayout($this->defaultLayout, $layoutName);
        $this->defaultLayout->setSubmitButton();

        return $this;
    }

    protected function initCallback(): void
    {
        $this->submitCb = Ajax::addAbstractTo($this);
        $this->valuesCb = Data::addAbstractTo($this);
    }

    /**
     * Get unique form identifier.
     * Use this identifier for element outside this form
     * by setting the 'form' attribute value on these elements.
     */
    public function getId(): string
    {
        return $this->getIdAttribute() . self::FORM_ID_SUFFIX;
    }

    /**
     * Return form controls values using Data request callback.
     * Each callback function will be called with parameters id and Form\Response\Value object.
     * Each callback function should take appropriate action to fill the Value instance with proper
     * input value and status.
     * When all callback are executed Data callback terminate with Value::getResponse() array.
     */
    public function onControlsValueRequest(\Closure $fx): self
    {
        $this->onHooks(self::HOOKS_GET_VALUES, $fx);

        $this->valuesCb->onDataRequest(function (array $payload = []): array {
            $response = new Form\Response\Value();
            if ($recordId = $payload['id'] ?? null) {
                $this->callHooks(self::HOOKS_GET_VALUES, HookFn::withVoid([$recordId, $response]));
            }

            return $response->getResponse();
        });

        return $this;
    }

    /**
     * Trigger valuesCb request from javascript in order to fill form control values.
     */
    public function jsRequestControlsValue(JsRenderInterface $id): JsRenderInterface
    {
        return $this->jsGetStore(self::PINIA_PREFIX)->setDataId($id);
    }

    public function jsClearControlsValue(): JsRenderInterface
    {
        return $this->jsGetStore(self::PINIA_PREFIX)->clearControlsValue();
    }

    public function getLayout(string $layoutName = self::MAIN_LAYOUT): ?FormLayoutInterface
    {
        return $this->layouts[$layoutName] ?? null;
    }

    public function getSubmitButton(string $layoutName = self::MAIN_LAYOUT): ?Button
    {
        return $this->getLayout($layoutName)->getSubmitButton();
    }

    public function addLayout(FormLayoutInterface $layout, string $layoutName = self::MAIN_LAYOUT): FormLayoutInterface
    {
        // @phpstan-ignore-next-line
        $this->addView($layout, 'Layouts');
        $this->layouts[$layoutName] = $layout;

        return $layout;
    }

    public function getControl(string $name): Control
    {
        return $this->controls[$name];
    }

    public function getControls(): array
    {
        return $this->controls;
    }

    public function hasControl(string $name): bool
    {
        return isset($this->controls[$name]);
    }

    public function getControlValues(): array
    {
        $values = [];
        foreach ($this->getControls() as $name => $control) {
            $values[$name] = $control->getValue();
        }

        return $values;
    }

    public function addControls(array $controls, string $layoutName = self::MAIN_LAYOUT): self
    {
        foreach ($controls as $control) {
            $this->addControl($control, $layoutName);
        }

        return $this;
    }

    public function addControl(Form\Control $control, string $layoutName = self::MAIN_LAYOUT, string $regionName = null): Form\Control
    {
        $this->registerControl($control);

        $this->callHook(self::HOOK_BEFORE_CONTROL_ADD, HookFn::withVoid([$this, $control, $layoutName]));

        return $this->getLayout($layoutName)->addControl($control, $regionName);
    }

    protected function registerControl(Control $control): void
    {
        $this->assertControlHasName($control->getControlName());
        $this->assertControlIsUnique($control->getControlName());
        $control->formStoreId = $this->getPiniaStoreId(self::PINIA_PREFIX);
        $this->controls[$control->getControlName()] = $control;
    }

    public function addHeader(View $header): self
    {
        $this->addView($header, 'header');

        return $this;
    }

    public function addFooter(View $footer): self
    {
        $this->addView($footer, 'footer');

        return $this;
    }

    /**
     * Adds callback in submit hook.
     */
    public function onSubmit(\Closure $function): self
    {
        $this->onHook(self::HOOK_SUBMIT, $function);

        $this->submitCb->onAjaxPostRequest(function (array $payload): JsRenderInterface {
            $recordId = isset($payload['__formRecordId']) ? (string) $payload['__formRecordId'] : null;
            unset($payload['__formRecordId']);
            $this->beforeSubmitHook($payload);
            $response = $this->callHook(self::HOOK_SUBMIT, HookFn::withJsRenderInterface([$this, $recordId]));
            $this->afterSubmitHook();

            return $response;
        });

        return $this;
    }

    /**
     * Add validation error to form.
     * Validation errors are catch after form submit before final response is return
     * and display in Ui.
     */
    public function addValidationError(string $controlName, string $message): self
    {
        if ($this->hasControl($controlName)) {
            $this->validationErrors[$controlName]['messages'][] = $message;
            $this->validationErrors[$controlName]['value'] = $this->getControl($controlName)->getValue();
        }

        return $this;
    }

    public function addValidationErrors(array $errors): self
    {
        foreach ($errors as $controlName => $error) {
            $this->addValidationError($controlName, $error);
        }

        return $this;
    }

    protected function beforeHtmlRender(): void
    {
        // todo move into props
        $this->getTemplate()->trySet('formId', $this->getId());
        $this->getTemplate()->trySet('loadingMsg', $this->loadingMsg);
        $this->setTemplateProps();

        $this->createVueApp(self::COMP_NAME, [], $this->getDefaultSelector());
        parent::beforeHtmlRender();
    }

    protected function setTemplateProps(): void
    {
        $props['storeId'] = $this->getPiniaStoreId(self::PINIA_PREFIX);
        $props['submitUrl'] = $this->submitCb->getUrl();
        $props['canLeave'] = $this->canLeave;
        $props['valuesUrl'] = $this->valuesCb ? $this->valuesCb->getUrl() : '';
        $props['dataRecordId'] = $this->dataRecordId;

        foreach ($props as $key => $value) {
            $this->getTemplate()->setJs($key, Type::factory($value));
        }
    }

    private function assertControlIsUnique(string $controlName): void
    {
        if (array_key_exists($controlName, $this->controls)) {
            throw (new Exception('This control is already added to form.'))
                ->addMoreInfo('Control name:', $controlName);
        }
    }

    private function assertControlHasName(string $controlName): void
    {
        if (!$controlName) {
            throw new Exception('Trying to add a form control without name.');
        }
    }

    /**
     * Set each control value using payload from ajax request and validate them.
     * When validation fail, collect control error in validationErrors property.
     */
    private function setControlsWithPostValue(array $payload): void
    {
        foreach ($this->getControls() as $controlName => $control) {
            $control->setWithPostValue($control->sanitize($payload[$controlName] ?? null));
            if ($errorMsg = $control->validate()) {
                $this->addValidationError($controlName, $errorMsg);
            }
        }
    }

    private function beforeSubmitHook(array $payload): void
    {
        $this->setControlsWithPostValue($payload);
        $this->validateForm();
    }

    private function afterSubmitHook(): void
    {
        $this->validateForm();
    }

    /**
     * Terminate callback when validationErrors contain control error.
     */
    private function validateForm(): void
    {
        if ($this->validationErrors) {
            $this->submitCb->terminateJson(['validationErrors' => $this->validationErrors]);
        }
    }
}
