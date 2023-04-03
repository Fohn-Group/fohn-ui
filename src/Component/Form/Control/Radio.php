<?php

declare(strict_types=1);

namespace Fohn\Ui\Component\Form\Control;

use Fohn\Ui\Js\Js;
use Fohn\Ui\Tailwind\Tw;

/**
 * Input element for a form control.
 */
class Radio extends Selection
{
    public string $defaultTemplate = 'vue-component/form/control/radio.html';
    public string $color = 'primary';
    public string $inputType = 'radio';

    public array $inputDefaultTws = [
        'border-gray-300',
        'shadow-sm',
        'focus:border-blue-300',
        'focus:ring',
        'focus:ring-offset-0',
        'focus:ring-blue-200',
        'focus:ring-opacity-50',
    ];

    public function getValue()
    {
        if (!parent::getValue()) {
            $this->setValue($this->getFirstItem());
        }

        return parent::getValue();
    }

    protected function beforeHtmlRender(): void
    {
        $this->inputTws->merge([Tw::colour($this->color, 'text')]);

        $this->getTemplate()->trySetJs('items', Js::array($this->getItems()));
        parent::beforeHtmlRender();
    }
}
