<?php

declare(strict_types=1);
/**
 * Table filter Vue Component.
 */

namespace Fohn\Ui\Component\Table;

use Fohn\Ui\Component\VueTrait;
use Fohn\Ui\View;

class Filter extends View
{
    use VueTrait;

    public string $defaultTemplate = 'vue-component/table/table-filter.html';
    public array $defaultTailwind = [
        'mx-2',
        'content-center',
        'text-blue-500',
        'text-xl',
    ];

    protected function initRenderTree(): void
    {
        parent::initRenderTree();
    }
}
