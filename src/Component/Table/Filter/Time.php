<?php

declare(strict_types=1);
/**
 * Filter for type time.
 */

namespace Fohn\Ui\Component\Table\Filter;

use Fohn\Ui\Component\Table\Filter;
use Fohn\Ui\Component\Utils;

class Time extends Date
{
    protected string $type = 'time';
}
