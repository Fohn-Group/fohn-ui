<?php

declare(strict_types=1);

/**
 * Chip View.
 */

namespace Fohn\Ui\View;

use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Theme\Base;
use Fohn\Ui\Tailwind\Tw;
use Fohn\Ui\View;

class Chip extends View
{
    public string $color = 'primary';
    public string $type = 'contained';
    public string $size = '24';
    public ?View $content = null;

    protected function initRenderTree(): void
    {
        parent::initRenderTree();
        if ($this->content === null) {
            $this->content = View::addTo($this)
                ->appendTailwinds(
                    [
                        Tw::flex('1'),
                        Tw::textAlign('center'),
                    ]
                );
        }
    }

    protected function beforeHtmlRender(): void
    {
        Ui::theme()::styleAs(Base::CHIP, [$this]);
        $this->content->htmlContent = $this->htmlContent;
        $this->htmlContent          = null;
        parent::beforeHtmlRender();
    }
}
