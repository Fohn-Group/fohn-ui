<?php

declare(strict_types=1);
/**
 * Table ModelCtrl Interface.
 */

namespace Fohn\Ui\Service;

use Fohn\Ui\Component\Table\Payload;
use Fohn\Ui\Component\Table\Result\Set;

interface TableModelControllerInterface
{
    public function setSearchFields(array $fields): void;

    public function setTableResultSet(Payload $payload, Set $resultSet): void;
}
