<?php

declare(strict_types=1);
/**
 * Generic Table column displaying a string value.
 */

namespace Fohn\Ui\Component\Table\Column;

use Fohn\Ui\Component\Table\Column;
use Fohn\Ui\Service\Ui;

class Number extends Column
{
    public string $defaultTemplate = 'vue-component/table/column/float.html';
    protected string $nullValue = '0';

    public ?string $locale = 'en_US';

    public array $defaultTailwind = [
        'text-right',
    ];

    /**
     *  Return display value.
     */
    public function getDisplayValue($value, $id): string
    {
        return $this->getFormatterValue($value) ?: $this->getNumberValue($value);
    }

    protected function getNumberValue(?float $value): string
    {
        return $value === null ? $this->nullValue : (string) $value;
    }

    protected function getLocale(): string
    {
        if (!$this->locale) {
            $this->locale = Ui::service()->locale();
        }

        return $this->locale;
    }
}
