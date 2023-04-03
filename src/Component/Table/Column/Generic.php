<?php

declare(strict_types=1);
/**
 * Generic Table column displaying a string value.
 */

namespace Fohn\Ui\Component\Table\Column;

class Generic extends \Fohn\Ui\Component\Table\Column
{
    public string $defaultTemplate = 'vue-component/table/column/string.html';

    /**
     *  Return display value.
     *  This value will be sanitized by fohn-table-cell component.
     */
    public function getDisplayValue($value, $id): string
    {
        return $this->getFormatterValue($value) ?: ($value ?? $this->nullValue);
    }
}
