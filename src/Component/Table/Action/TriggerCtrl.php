<?php

declare(strict_types=1);
/**
 * Perform a callback request event from a Table/Action view.
 */

namespace Fohn\Ui\Component\Table\Action;

use Fohn\Ui\Component\Table\Action;
use Fohn\Ui\Js\JsRenderInterface;

class TriggerCtrl
{
    private Action $tableAction;

    public function __construct(Action $tableAction)
    {
        $this->tableAction = $tableAction;
    }

    public function onTrigger(\Closure $fx): void
    {
        $extra = ['state' => ['reload' => $this->tableAction->reloadTable, 'keepSelection' => $this->tableAction->keepSelection]];
        $this->tableAction->getCallback()->onAjaxPostRequest(function (array $payload) use ($fx): JsRenderInterface {
            return $fx($payload['ids'], $this->tableAction->getConfirmationModal());
        }, $extra);
    }
}
