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
    public string $searchQuery;

    public function __construct(array $payload)
    {
        $this->page = $payload['page'] ?? 0;
        $this->sortColumn = $payload['sorting']['columnName'] ?? '';
        $this->sortDirection = $payload['sorting']['direction'] ?? '';
        $this->ipp = $payload['ipp'] ?? 10;
        $this->searchQuery = $payload['_q'] ?? '';
    }
}
