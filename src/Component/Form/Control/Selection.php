<?php

declare(strict_types=1);

namespace Fohn\Ui\Component\Form\Control;

class Selection extends Input
{
    public const KEY = 'key';
    public const LABEL = 'label';

    /** @var array<array<string, string>> A list of items to choose from. */
    private array $items = [];

    protected function initRenderTree(): void
    {
        parent::initRenderTree();
    }

    /**
     * Set selectableItems using a key => value pair array.
     */
    public function setItems(array $items): self
    {
        foreach ($items as $k => $v) {
            $this->items[] = [self::KEY => $k, self::LABEL => $v];
        }

        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return mixed
     */
    protected function getFirstItem()
    {
        return $this->items[0][self::KEY];
    }
}
