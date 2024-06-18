<?php

declare(strict_types=1);
/**
 * Filter for type datetime.
 */

namespace Fohn\Ui\Component\Table\Filter;

use Fohn\Ui\Component\Table\Filter;
use Fohn\Ui\Component\Utils;

class DateTime extends Date
{
    protected string $type = 'datetime';
}
