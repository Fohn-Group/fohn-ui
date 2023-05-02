<?php

declare(strict_types=1);

namespace Fohn\Ui\Component\Form\Layout;

use Fohn\Ui\Component\Form\Control;
use Fohn\Ui\View;
use Fohn\Ui\View\Button;

interface FormLayoutInterface
{
    public function setSubmitButton(Button $button = null): self;

    public function addButton(Button $button): Button;

    public function addControl(Control $control, string $regionName): Control;

    public function getSubmitButton(): ?Button;

    /**
     * Prevent submit button from rendering in Layout.
     * User is responsible to render Form::submitButton into another
     * view than Form Layout.
     */
    public function addSubmitButton(bool $add): void;
}
