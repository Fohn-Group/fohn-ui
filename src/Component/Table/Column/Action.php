<?php

declare(strict_types=1);
/**
 * Represent a Js action column.
 */

namespace Fohn\Ui\Component\Table\Column;

use Fohn\Ui\View;

class Action extends Generic implements ActionInterface
{
    public string $defaultTemplate = 'vue-component/table/column/action.html';

    protected function beforeHtmlRender(): void
    {
        $this->clearIdAttribute($this);
        parent::beforeHtmlRender();
    }

    /**
     * Action column content is duplicate within each row of a table,
     * therefore, make sure that each Views inside column has the Id
     * attribute set to ''.
     */
    private function clearIdAttribute(View $view): void
    {
        foreach ($view->getViewElements() as $v) {
            $v->setIdAttribute('');
            $this->clearIdAttribute($v);
        }
    }
}
