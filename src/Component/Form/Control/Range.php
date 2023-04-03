<?php

declare(strict_types=1);
/**
 * Range Input.
 */

namespace Fohn\Ui\Component\Form\Control;

use Fohn\Ui\Js\Js;
use Fohn\Ui\Tailwind\Tw;

class Range extends Input
{
    public string $defaultTemplate = 'vue-component/form/control/range.html';

    public string $inputType = 'range';
    public string $color = 'primary';
    public int $minValue = 0;
    public int $maxValue = 100;
    public int $step = 1;

    public function setWithPostValue(?string $value): void
    {
        if ($value !== null) {
            $this->setValue((int) $value);
        }
    }

    protected function beforeHtmlRender(): void
    {
        $this->getTemplate()->trySetJs('step', Js::string((string) $this->step));
        $this->getTemplate()->trySetJs('max', Js::integer($this->maxValue));
        $this->getTemplate()->trySetJs('min', Js::integer($this->minValue));

        $this->inputTws->merge([Tw::colour($this->color, 'accent')]);

        parent::beforeHtmlRender();
    }
}
