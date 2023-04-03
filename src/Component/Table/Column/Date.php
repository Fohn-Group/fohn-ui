<?php

declare(strict_types=1);
/**
 * Display date time.
 */

namespace Fohn\Ui\Component\Table\Column;

class Date extends Generic
{
    public string $format = 'Y-m-d';

    public function getDisplayValue($value, $id): string
    {
        return $this->getFormatterValue($value) ?: $this->getDateValue($value);
    }

    protected function getDateValue(?\DateTime $date): string
    {
        return $date === null ? $this->nullValue : $date->format($this->format);
    }
}
