<?php

declare(strict_types=1);
/**
 * Ui Service setup for testing purpose.
 */

namespace Fohn\Ui\Tests;

use Fohn\Ui\Service\Ui;

class UiTestService extends Ui
{
    protected function returnQueryParamValue(string $param): ?string
    {
        return $_GET[$param] ?? null;
    }
}
