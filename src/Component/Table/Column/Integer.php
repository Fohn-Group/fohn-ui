<?php

declare(strict_types=1);
/**
 * Generic Table column displaying a string value.
 */

namespace Fohn\Ui\Component\Table\Column;

use Fohn\Ui\Component\Table\Column;

class Integer extends Column
{
    public string $defaultTemplate = 'vue-component/table/column/integer.html';
    protected string $nullValue = '0';

    public array $defaultTailwind = [
        'text-center',
    ];

    /**
     *  Return display value.
     *  This value will be sanitized by fohn-table-cell component.
     */
    public function getDisplayValue($value, $id): string
    {
        return $this->getFormatterValue($value) ?: $this->getIntegerValue($value);
    }

    protected function getIntegerValue(?int $value): string
    {
        return $value === null ? $this->nullValue : (string) $value;
    }
}
