<?php

declare(strict_types=1);

namespace Fohn\Ui\View;

use Fohn\Ui\Tailwind\Tw;
use Fohn\Ui\View;

class GridLayout extends View
{
    public array $defaultTailwind = [
        'grid',
    ];

    protected int $rows = 1;
    protected int $gap = 1;
    protected int $columns = 1;

    protected string $direction = 'row';

    /**
     * Initialization.
     */
    protected function initRenderTree(): void
    {
        parent::initRenderTree();
    }

    protected function beforeHtmlRender(): void
    {
        $this->appendTailwinds([
            $this->columns ? Tw::gridType('cols', (string) $this->columns) : '',
            $this->rows ? Tw::gridType('rows', (string) $this->rows) : '',
            Tw::gridAutoFlow($this->direction),
            Tw::utility('gap', (string) $this->gap),
        ]);
        parent::beforeHtmlRender();
    }
}
