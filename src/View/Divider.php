<?php

declare(strict_types=1);

namespace Fohn\Ui\View;

use Fohn\Ui\View;

class Divider extends View
{
    protected function initRenderTree(): void
    {
        parent::initRenderTree();

        $this->htmlTag = 'hr';
        $this->setIdAttribute('');

        $this->appendTailwinds(
            [
                'my-4',
            ]
        );
    }
}
