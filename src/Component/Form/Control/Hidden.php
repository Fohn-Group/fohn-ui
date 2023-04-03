<?php

declare(strict_types=1);

namespace Fohn\Ui\Component\Form\Control;

/**
 * Input element for a form control.
 */
class Hidden extends Input
{
    public string $defaultTemplate = 'vue-component/form/control/hidden.html';
    public string $inputType = 'hidden';
}
