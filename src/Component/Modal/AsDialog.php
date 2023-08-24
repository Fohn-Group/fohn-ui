<?php

declare(strict_types=1);
/**
 * Modal that act as a dialog.
 * Buttons can be added to the dialog and each button can fire an ajax callback request.
 */

namespace Fohn\Ui\Component\Modal;

use Fohn\Ui\Callback\Ajax;
use Fohn\Ui\Component\Modal;
use Fohn\Ui\Component\VueTrait;
use Fohn\Ui\Core\HookTrait;
use Fohn\Ui\Js\JsFunction;
use Fohn\Ui\Js\JsRenderInterface;
use Fohn\Ui\Js\Type\ObjectLiteral;
use Fohn\Ui\Js\Type\StringLiteral;
use Fohn\Ui\Js\Type\Variable;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\View\Button;

class AsDialog extends Modal
{
    use HookTrait;

    public const HOOK_CONFIRM = self::class . '@confirm';
    public const HOOK_CANCEL = self::class . '@cancel';

    /** @var array<string, Ajax> */
    private array $callbacks = [];

    protected ?Button $confirmButton = null;
    protected ?Button $cancelButton = null;

    protected function initRenderTree(): void
    {
        parent::initRenderTree();
    }

    public function addCallbackEvent(string $name, Button $trigger, array $payload = [], string $eventName = 'click'): self
    {
        $this->callbacks[$name] = Ajax::addAbstractTo($this);
        $eventFn = JsFunction::declareFunction('onCallback', [Variable::set('$event'), StringLiteral::set($name), ObjectLiteral::set($payload)]);
        $this->addView($trigger, 'Buttons');
        static::bindVueEvent($trigger, $eventName, $eventFn->jsRender());

        return $this;
    }

    public function onCallbackEvent(string $name, \Closure $fx): self
    {
        $callback = $this->callbacks[$name];
        $callback->onAjaxPostRequest(function (array $payload) use ($fx): JsRenderInterface {
            return $fx($payload);
        });

        return $this;
    }

    public function jsSetMessage(string $msg) {

    }

    protected function beforeHtmlRender(): void
    {
        $renderCallbacks = [];
        foreach ($this->callbacks as $name => $callback) {
            $renderCallbacks[$name] = $callback->getUrl();
        }

        $this->getTemplate()->trySetJs('callbacks', ObjectLiteral::set($renderCallbacks));
        parent::beforeHtmlRender();
    }
}
