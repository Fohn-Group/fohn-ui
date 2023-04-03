<?php

declare(strict_types=1);
/**
 * Password input with Eye icon in order to reveal password value.
 */

namespace Fohn\Ui\Component\Form\Control;

class Password extends Input
{
    public string $defaultTemplate = 'vue-component/form/control/password.html';
    public string $inputType = 'password';
}
