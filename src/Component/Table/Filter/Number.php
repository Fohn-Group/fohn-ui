<?php

declare(strict_types=1);
/**
 *  Float filter value.
 */

namespace Fohn\Ui\Component\Table\Filter;

class Number extends Generic implements FilterColumnInterface
{
    protected string $type = 'number';

    protected array $props = [
        'type' => 'number',
        'precision' => 2,
    ];

    public function getValue(?string $value): ?float
    {
        return (float) $value;
    }
}
