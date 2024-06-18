<?php

declare(strict_types=1);
/**
 * Filter for type number.
 */

namespace Fohn\Ui\Component\Table\Filter;

class Integer extends Generic implements FilterColumnInterface
{
    protected string $type = 'number';

    protected array $props = [
        'type' => 'number',
    ];

    public function getValue(string $value): int
    {
        return (int) $value;
    }
}
