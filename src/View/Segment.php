<?php

declare(strict_types=1);

/**
 * Segment.
 */

namespace Fohn\Ui\View;

use Fohn\Ui\Tailwind\Tw;
use Fohn\Ui\View;

class Segment extends View
{
    public string $borderRadius = 'none';
    public string $borderWidth = '1';
    public string $padding = '4';
    public string $borderColor = '';
    public string $yValue = '2';

    protected function initRenderTree(): void
    {
        parent::initRenderTree();

        $this->appendCssClasses('fohn-segment');

        $this->setTailwinds([
            Tw::padding($this->padding),
            Tw::border($this->borderWidth),
            Tw::borderRadius($this->borderRadius),
            Tw::marginY($this->yValue),
        ]);

        if ($this->borderColor) {
            $this->appendTailwind(Tw::borderColor($this->borderColor));
        }
    }
}
