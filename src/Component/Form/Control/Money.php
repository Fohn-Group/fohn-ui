<?php

declare(strict_types=1);
/**
 * Money type input.
 */

namespace Fohn\Ui\Component\Form\Control;

class Money extends Number
{
    public string $defaultTemplate = 'vue-component/form/control/money.html';
    public string $currencySymbol = '$';
    public ?int $precision = 2;

    public function beforeHtmlRender(): void
    {
        $this->inputTws->merge(['pl-6']);
        $this->template->trySet('symbol', $this->currencySymbol);

        parent::beforeHtmlRender();
    }
}
