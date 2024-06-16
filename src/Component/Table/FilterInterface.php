<?php

declare(strict_types=1);
/**
 * Table Filter Interface.
 */

namespace Fohn\Ui\Component\Table;

use Fohn\Ui\Component\Table\Filter\FilterColumnInterface;

interface FilterInterface
{
    public function addColumnFilter(FilterColumnInterface $columnFilter): FilterColumnInterface;

    public function getColumnFilter(string $columnFilterId): FilterColumnInterface;
}
