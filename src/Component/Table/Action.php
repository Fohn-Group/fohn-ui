<?php

declare(strict_types=1);
/**
 * Create an action that can be trigger using multiple rows selected.
 */

namespace Fohn\Ui\Component\Table;

use Fohn\Ui\Callback\Ajax;
use Fohn\Ui\Component\VueTrait;
use Fohn\Ui\View;

class Action extends View
{
    use VueTrait;

    public string $defaultTemplate = 'vue-component/table/table-action.html';

    /** Keep selection after action is trigger. */
    public bool $keepSelection = false;
    /** Reload table data after action is trigger. */
    public bool $reloadTable = false;
    protected ?View\Button $trigger = null;
    protected ?Ajax $cb = null;

    protected function initRenderTree(): void
    {
        parent::initRenderTree();
        $this->cb = Ajax::addAbstractTo($this);
    }

    public function getCallback(): Ajax
    {
        return $this->cb;
    }

    public function setTrigger(View\Button $btn): self
    {
        $this->trigger = $btn;
        $this->addView($btn);

        return $this;
    }

    protected function beforeHtmlRender(): void
    {
        $this->getTemplate()->set('actionUrl', $this->cb->getUrl());
        static::bindVueEvent($this->trigger, 'click', 'execute');
        static::bindVueAttr($this->trigger, 'disabled', '!isEnable || isTableFetching');

        parent::beforeHtmlRender();
    }
}
