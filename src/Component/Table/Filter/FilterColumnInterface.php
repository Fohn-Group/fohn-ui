<?php

declare(strict_types=1);
/**
 * Filter Interface.
 */

namespace Fohn\Ui\Component\Table\Filter;

interface FilterColumnInterface
{
    public function getId(): string;

    public function getDefinition(): array;

    /** @return mixed */
    public function getValue(string $value);
}
