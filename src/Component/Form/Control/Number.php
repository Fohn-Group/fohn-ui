<?php

declare(strict_types=1);
/**
 * Float type.
 */

namespace Fohn\Ui\Component\Form\Control;

use Fohn\Ui\Js\Js;

class Number extends Input
{
    public string $defaultTemplate = 'vue-component/form/control/number.html';

    public string $inputType = 'number';
    /** The number of digit supported by the number. */
    public ?int $precision = null;
    /** The number of incrementation for step html attribute value.  */
    public int $incrementBy = 1;

    protected function beforeHtmlRender(): void
    {
        $this->getTemplate()->trySetJs('step', Js::string($this->precision !== null ? $this->getStep() : 'any'));

        parent::beforeHtmlRender();
    }

    public function setWithPostValue(?string $value): void
    {
        if ($value !== null) {
            $this->setValue($this->precision ? (float) $value : (int) $value);
        }
    }

    /**
     * Return proper step value based on precision number and incrementBy value.
     * ex:
     *   $precision = 4;
     *   $incrementBy = 5;.
     *
     *   Will return '.0005' which is four digit precision by step of 5.
     */
    protected function getStep(): string
    {
        $step = 0;
        if ($this->precision > 0) {
            $v = (10 ** $this->precision) * $this->precision;

            $step = ($this->precision / $v) * $this->incrementBy;
        }

        return (string) $step;
    }
}
