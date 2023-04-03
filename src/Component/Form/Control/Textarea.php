<?php

declare(strict_types=1);

namespace Fohn\Ui\Component\Form\Control;

use Fohn\Ui\Component\Form\Control;

/**
 * Input element for a form control.
 */
class Textarea extends Input
{
    public string $defaultTemplate = 'vue-component/form/control/textarea.html';

    public string $rows = '3';

    protected function beforeHtmlRender(): void
    {
        $this->getTemplate()->trySet('rows', $this->rows);

        parent::beforeHtmlRender();
    }
}
