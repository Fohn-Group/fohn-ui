<?php

declare(strict_types=1);
/**
 * Contains a Select items value.
 */

namespace Fohn\Ui\Component\Form\Response;

class Items
{
    private array $items = [];

    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function getResponse(): array
    {
        return $this->items;
    }
}
