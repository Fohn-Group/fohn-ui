<?php

declare(strict_types=1);

namespace Fohn\Ui\View;

use Fohn\Ui\View;

class Image extends View
{
    public string $defaultTemplate = 'view/image.html';

    /** @var string */
    public $src = '';

    protected function beforeHtmlRender(): void
    {
        $this->getTemplate()->set('source', $this->src);
        parent::beforeHtmlRender();
    }
}
