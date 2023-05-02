<?php

declare(strict_types=1);

namespace Fohn\Ui\Component\Form\Layout;

use Fohn\Ui\Component\Form\Control;
use Fohn\Ui\View;
use Fohn\Ui\View\Button;

/**
 * Provides generic layout for a form.
 */
class Standard extends View implements FormLayoutInterface
{
    public string $defaultTemplate = 'vue-component/form/layout/standard.html';
    protected const CTRL_REGION_PREFIX = 'ctrl_';

    public ?Button $submitButton = null;
    public array $defaultSubmitBtnSeed = [Button::class, 'label' => 'Save', 'color' => 'primary'];
    public bool $addSubmitBtnInLayout = true;

    public function addButton(Button $button): Button
    {
        return $button::addTo($this, [], 'Buttons');
    }

    public function getSubmitButton(): ?Button
    {
        return $this->submitButton;
    }

    public function addSubmitButton(bool $add): void
    {
        $this->addSubmitBtnInLayout = false;
    }

    public function setSubmitButton(Button $button = null): self
    {
        if (!$button) {
            $button = Button::factoryFromSeed($this->defaultSubmitBtnSeed);
        }
        $this->submitButton = $button;
        $this->submitButton->appendHtmlAttributes(['type' => 'submit']);

        return $this;
    }

    public function addControl(Control $control, string $regionName = null): Control
    {
        if (!$regionName) {
            $regionName = $this->template->hasTag(self::CTRL_REGION_PREFIX . $control->getControlName()) ? self::CTRL_REGION_PREFIX . $control->getControlName() : View::MAIN_TEMPLATE_REGION;
        }
        $this->addView($control, $regionName);

        return $control;
    }

    protected function beforeHtmlRender(): void
    {
        if ($this->addSubmitBtnInLayout) {
            $this->addView($this->submitButton, 'Buttons');
        }

        parent::beforeHtmlRender();
    }
}
