<?php

declare(strict_types=1);

namespace Fohn\Ui\View;

use Fohn\Ui\View;

class Icon extends View
{
    public string $defaultTemplate = 'view/icon.html';

    /** @var string */
    public $iconName = '';

    /** @var string */
    public $size;

    protected function beforeHtmlRender(): void
    {
        $this->appendCssClasses($this->iconName);

        parent::beforeHtmlRender();
    }
}
