<?php

declare(strict_types=1);
/**
 * Html List.
 */

namespace Fohn\Ui\View;

use Fohn\Ui\Service\Ui;

class HtmlList extends Lister
{
    protected const ITEMS_TAG_NAME = 'Items';

    public string $defaultTemplate = '/view/html-ordered-list.html';
    public string $itemsTemplate = '/view/html-list-item.html';

    public string $style = 'disc';
    public string $position = 'inside';

    public function setItems(array $items): self
    {
        $this->setRegionItems(static::ITEMS_TAG_NAME, $items, Ui::templateFromFile($this->itemsTemplate));

        return $this;
    }

    public function beforeHtmlRender(): void
    {
        $this->appendTailwind('list-' . $this->style);
        $this->appendTailwind('list-' . $this->position);

        parent::beforeHtmlRender();
    }
}
