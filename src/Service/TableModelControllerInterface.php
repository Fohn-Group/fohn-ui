<?php

declare(strict_types = 1);
/**
 * Table ModelCtrl Interface.
 */

namespace Fohn\Ui\Service;

use Fohn\Ui\Component\Table\Payload;
use Fohn\Ui\Component\Table\Result\Set;

interface TableModelControllerInterface
{
    public function setSearchFields(array $fields): void;
    public function getDataSet(Payload $payload): array;
    public function setTableResultSet(Set $resultSet, Payload $payload): void;
}
