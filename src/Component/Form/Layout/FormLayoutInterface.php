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

    public function setButtonContainer(View $view): self;
}
