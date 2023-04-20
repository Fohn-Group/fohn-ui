<?php

declare(strict_types=1);
/**
 * Use v-html directive to render link.
 * Make sure value are properly escape.
 */

namespace Fohn\Ui\Component\Table\Column;

use Fohn\Ui\Service\Ui;

class Link extends Html
{
    public string $url = '#';

    public bool $hasIdParam = true;
    public string $idParamName = 'id';
    public array $params = [];

    public function getDisplayValue($value, $id): string
    {
        return $this->getFormatterValue($value) ?: $this->getLinkValue($value, (string) $id);
    }

    protected function getLinkValue(?string $value, string $id): string
    {
        $params = $this->params;
        if ($this->hasIdParam) {
            $params[$this->idParamName] = $id;
        }

        $link = \Fohn\Ui\View\Link::factory(['url' => Ui::buildUrl($this->url, $params)]);
        $link->setTextContent($value ?? $this->nullValue);

        return $link->getHtml();
    }
}
