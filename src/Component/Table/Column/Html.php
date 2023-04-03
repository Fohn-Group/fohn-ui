<?php

declare(strict_types=1);
/**
 * Table cell that display html value.
 */

namespace Fohn\Ui\Component\Table\Column;

class Html extends Generic
{
    /** Sanitize value by default. Make sure you trust html value content when not sanitizing. */
    public bool $sanitize = true;
    public string $defaultTemplate = 'vue-component/table/column/html.html';

    public function getDisplayValue($value, $id): string
    {
        return $this->getFormatterValue($value) ?: $this->getHtmlValue($value);
    }

    protected function getHtmlValue(string $value): string
    {
        return $this->sanitize ? htmlspecialchars($value ?: $this->nullValue) : $value;
    }
}
