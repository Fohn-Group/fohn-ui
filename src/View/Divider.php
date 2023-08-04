<?php

declare(strict_types=1);

namespace Fohn\Ui\View;

use Fohn\Ui\View;

class Divider extends View
{
    public string $verticalSpace = '4';

    protected function initRenderTree(): void
    {
        parent::initRenderTree();

        $this->htmlTag = 'hr';
        $this->setIdAttribute('');
    }

    public function beforeHtmlRender(): void
    {
        $this->appendTailwinds(
            [
                'my-' . $this->verticalSpace,
            ]
        );

        parent::beforeHtmlRender();
    }
}
