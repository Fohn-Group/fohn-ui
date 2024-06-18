<?php

declare(strict_types=1);
/**
 * Filter for type date.
 */

namespace Fohn\Ui\Component\Table\Filter;

use Fohn\Ui\Component\Utils;

class Date extends Generic implements FilterColumnInterface
{
    protected string $type = 'date';
    protected string $format;

    public function __construct(string $id, string $format = 'Y-m-d', string $label = null, array $props = [])
    {
        $props['config'] = Utils::getFlatPickrConfig($this->type, $format);
        $props['config']['allowInput'] = true;
        $this->format = $format;

        parent::__construct($id, $label, $props);
    }

    public function getValue(string $value): ?\DateTime
    {
        return \DateTime::createFromFormat($this->format, $value) ?: null;
    }
}
