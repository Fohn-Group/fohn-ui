<?php

declare(strict_types=1);
/**
 * Link.
 */

namespace Fohn\Ui\View;

use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Theme\Base;
use Fohn\Ui\View;

class Link extends View
{
    public string $url = '#';
    public string $color = 'primary';
    public string $decorationColor = 'primary';

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function beforeHtmlRender(): void
    {
        $this->htmlTag = 'a';
        $this->appendHtmlAttribute('href', $this->url);

        Ui::theme()->styleAs(Base::LINK, [$this]);

        parent::beforeHtmlRender();
    }
}
