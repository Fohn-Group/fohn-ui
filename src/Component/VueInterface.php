<?php

declare(strict_types=1);
/**
 * Vue Component Interface.
 */

namespace Fohn\Ui\Component;

interface VueInterface
{
    public function isRootComponent(): bool;
}
