<?php

declare(strict_types=1);
/**
 * Use v-html directive to render mailTo.
 */

namespace Fohn\Ui\Component\Table\Column;

class Email extends Html
{
    public function getDisplayValue($value, $id): string
    {
        return $this->getFormatterValue($value) ?: $this->getLinkValue($value, (string) $id);
    }

    protected function getLinkValue(?string $value, string $id): string
    {
        $fvalue = $value ?? $this->nullValue;
        $link = \Fohn\Ui\View\Link::factory(['url' => 'mailto:' . $fvalue]);
        $link->setTextContent($fvalue);

        return $link->getHtml();
    }
}
