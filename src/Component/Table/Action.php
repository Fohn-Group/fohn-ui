<?php

declare(strict_types=1);
/**
 * Create an action that can be trigger using multiple rows selected.
 */

namespace Fohn\Ui\Component\Table;

use Fohn\Ui\Callback\Ajax;
use Fohn\Ui\Component\Modal\AsDialog;
use Fohn\Ui\Component\VueTrait;
use Fohn\Ui\Js\Js;
use Fohn\Ui\View;

class Action extends View
{
    use VueTrait;

    public string $defaultTemplate = 'vue-component/table/table-action.html';

    public bool $requireSelection = true;
    /** Keep selection after action is trigger. */
    public bool $keepSelection = false;
    /** Reload table data after action is trigger. */
    public bool $reloadTable = false;
    protected ?View\Button $trigger = null;
    protected ?Ajax $cb = null;

    protected ?AsDialog $confirmationModal = null;

    protected function initRenderTree(): void
    {
        parent::initRenderTree();
        $this->cb = Ajax::addAbstractTo($this);
    }

    public function getCallback(): Ajax
    {
        return $this->cb;
    }

    public function getConfirmationModal(): AsDialog
    {
        return $this->confirmationModal;
    }

    public function addConfirmationDialog(string $title, string $msg, View\Button $ok = null, View\Button $cancel = null, bool $isClosable = true): self
    {
        $this->confirmationModal = AsDialog::addTo($this, ['title' => $title, 'isClosable' => $isClosable]);
        $this->confirmationModal->setTextContent($msg);
        if (!$isClosable) {
            $this->confirmationModal->addCancelEvent();
        }

        $trigger = $this->confirmationModal->addConfirmEvent();
        $this->confirmationModal->addEvent('on-confirm', Js::var('execute($event)'));
        static::bindVueEvent($trigger, 'click', 'confirm');

        return $this;
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
        if ($this->confirmationModal) {
            $this->trigger->appendHtmlAttribute('onclick', $this->confirmationModal->jsOpen()->jsRender());
        } else {
            static::bindVueEvent($this->trigger, 'click', 'execute');
        }
        static::bindVueAttr($this->trigger, 'disabled', '!isEnable || isTableFetching');

        $this->renderEvents();
        parent::beforeHtmlRender();
    }
}
