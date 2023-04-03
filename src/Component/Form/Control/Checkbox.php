<?php

declare(strict_types=1);

namespace Fohn\Ui\Component\Form\Control;

use Fohn\Ui\Tailwind\Tw;

/**
 * Input element for a form control.
 */
class Checkbox extends Input
{
    public string $defaultTemplate = 'vue-component/form/control/checkbox.html';
    public string $color = 'primary';

    public string $inputType = 'checkbox';

    /** @var array|string[] */
    public array $inputDefaultTws = [
        'rounded',
        'border-gray-300',
        'text-indigo-600',
        'shadow-sm',
        'focus:border-blue-300',
        'focus:ring',
        'focus:ring-offset-0',
        'focus:ring-blue-200',
        'focus:ring-opacity-50',
    ];

    public function setWithPostValue(?string $value): void
    {
        $this->setValue((bool) $value);
    }

    protected function beforeHtmlRender(): void
    {
        $this->inputTws->merge([Tw::colour($this->color, 'text')]);
        $this->removeTailwind('mb-4');

        parent::beforeHtmlRender();
    }
}
