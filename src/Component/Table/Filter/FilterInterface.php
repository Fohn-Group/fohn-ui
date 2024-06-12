<?php

declare(strict_types=1);
/**
 * Filter Interface.
 */

namespace Fohn\Ui\Component\Table\Filter;

interface FilterInterface
{
    public function getId(): string;

    public function getDefinition(): array;
}
