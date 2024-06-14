<?php

declare(strict_types=1);
/**
 * Ui Service setup for testing purpose.
 */

namespace Fohn\Ui\Tests\Concerns;

use Fohn\Ui\Service\Ui;

class MockUi extends Ui
{
    public function getQueryParamValue(string $param): ?string
    {
        return $_GET[$param] ?? null;
    }
}
