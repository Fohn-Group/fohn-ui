<?php

declare(strict_types=1);
/**
 * Convert a table payload request into an object.
 */

namespace Fohn\Ui\Component\Table;

class Payload
{
    public int $page;
    public string $sortColumn;
    public string $sortDirection;
    public int $ipp;
    public array $filters;

    public string $searchQuery;

    public function __construct(array $payload, FilterInterface $filter)
    {
        $this->page = $payload['page'] ?? 0;
        $this->sortColumn = $payload['sorting']['columnName'] ?? '';
        $this->sortDirection = $payload['sorting']['direction'] ?? '';
        $this->ipp = $payload['ipp'] ?? 10;
        $this->searchQuery = $payload['_q'] ?? '';
        $this->filters = $this->getFilterValues($payload['filters'] ?? [], $filter);
    }

    private function getFilterValues(array $filterPayload, FilterInterface $filter): array
    {
        foreach ($filterPayload['columns'] as $k => $col) {
            $filterPayload['columns'][$k]['filterValue'] = $filter->getColumnFilter($col['column'])->getValue($col['value']);
        }

        return $filterPayload;
    }
}
