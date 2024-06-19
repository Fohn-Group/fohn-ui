<?php

declare(strict_types=1);
/**
 * Filter for type text.
 */

namespace Fohn\Ui\Component\Table\Filter;

class Text extends Generic implements FilterColumnInterface
{
    public function getValue(?string $value): ?string
    {
        return $value;
    }
}
