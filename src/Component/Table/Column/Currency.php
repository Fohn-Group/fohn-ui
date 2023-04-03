<?php

declare(strict_types=1);
/**
 * Generic Table column displaying a string value.
 */

namespace Fohn\Ui\Component\Table\Column;

class Currency extends Number
{
    public ?string $currencyCode = 'USD';

    /** Determine how to display negative value. When set, it will display value as (10) instead of -10. */
    public bool $isAccounting = false;

    /**
     *  Return display value.
     */
    public function getDisplayValue($value, $id): string
    {
        return $this->getFormatterValue($value) ?: $this->getCurrencyValue($value);
    }

    protected function getCurrencyValue(?float $value): string
    {
        $formatterOption = $this->isAccounting ? \NumberFormatter::CURRENCY_ACCOUNTING : \NumberFormatter::CURRENCY;
        $value = $value === null ? 0.0 : $value;
        $formatter = new \NumberFormatter($this->locale, $formatterOption);
        $newValue = $formatter->formatCurrency($value, $this->currencyCode);

        return $newValue;
    }
}
