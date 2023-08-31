<?php

declare(strict_types=1);
/**
 * Modal that act as a dialog.
 * Buttons can be added to the dialog and each button can fire an ajax callback request.
 */

namespace Fohn\Ui\Component\Modal;

use Fohn\Ui\Callback\Ajax;
use Fohn\Ui\Component\Modal;
use Fohn\Ui\Js\JsFunction;
use Fohn\Ui\Js\JsRenderInterface;
use Fohn\Ui\Js\Type\ObjectLiteral;
use Fohn\Ui\Js\Type\StringLiteral;
use Fohn\Ui\Js\Type\Variable;
use Fohn\Ui\View;
use Fohn\Ui\View\Button;

class AsDialog extends Modal
{
    /** @var array<string, Ajax> */
    private array $callbacks = [];

    public array $cancelButtonSeed = [Button::class, 'label' => 'No', 'type' => 'outline', 'color' => 'error', 'size' => 'small'];
    public array $confirmButtonSeed = [Button::class, 'label' => 'Yes', 'type' => 'outline', 'color' => 'success', 'size' => 'small'];

    /**
     * Add a close event to modal using Closure function.
     * When calling this method with no Closure function, the modal will
     * close without triggering a callback event.
     */
    public function addCancelEvent(\Closure $fx = null, View $trigger = null): View
    {
        $cancelTrigger = $trigger ?: View::factoryFromSeed($this->cancelButtonSeed);
        if ($fx) {
            $this->addCallbackEvent('cancel', $cancelTrigger);
            $this->onCallbackEvent('cancel', $fx);
        } else {
            $this->addView($cancelTrigger, 'Buttons');
            static::bindVueEvent($cancelTrigger, 'click', 'closeModal(true)');
        }

        return $cancelTrigger;
    }

    public function addConfirmEvent(\Closure $fx = null, View $trigger = null): View
    {
        $confirmTrigger = $trigger ?: View::factoryFromSeed($this->confirmButtonSeed);
        if ($fx) {
            $this->addCallbackEvent('confirm', $confirmTrigger);
            $this->onCallbackEvent('confirm', $fx);
        } else {
            $this->addView($confirmTrigger, 'Buttons');
        }

        return $confirmTrigger;
    }

    public function addCallbackEvent(string $name, View $trigger, array $payload = [], string $eventName = 'click'): View
    {
        $this->callbacks[$name] = Ajax::addAbstractTo($this);
        $eventFn = JsFunction::declareFunction('onCallback', [Variable::set('$event'), StringLiteral::set($name), ObjectLiteral::set($payload)]);
        $this->addView($trigger, 'Buttons');
        static::bindVueEvent($trigger, $eventName, $eventFn->jsRender());

        return $trigger;
    }

    public function onCallbackEvent(string $name, \Closure $fx): self
    {
        $callback = $this->callbacks[$name];
        $callback->onAjaxPostRequest(function (array $payload) use ($fx): JsRenderInterface {
            return $fx($payload);
        });

        return $this;
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
