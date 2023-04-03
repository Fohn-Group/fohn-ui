<?php

declare(strict_types=1);
/**
 * Display boolean value using icon.
 */

namespace Fohn\Ui\Component\Table\Column;

use Fohn\Ui\Component\Table\Column;

class Boolean extends Column
{
    /** css value for icons. */
    public string $falseIcon = 'bi bi-dash';
    public string $trueIcon = 'bi bi-check';

    public string $defaultTemplate = 'vue-component/table/column/boolean.html';

    public function getDisplayValue($value, $id): string
    {
        return $this->getFormatterValue($value) ?: $this->getBoolValue($value);
    }

    protected function getBoolValue(?bool $value): string
    {
        return $value === null ? $this->nullValue : (string) $value;
    }

    public function beforeHtmlRender(): void
    {
        $this->getTemplate()->set('falseIcon', $this->falseIcon);
        $this->getTemplate()->set('trueIcon', $this->trueIcon);
        parent::beforeHtmlRender();
    }
}
