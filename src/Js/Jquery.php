<?php

declare(strict_types=1);

namespace Fohn\Ui\Js;

use Fohn\Ui\View;

/**
 * Implements mapper for jQuery library.
 *
 * @method Jquery accordion(...$args)
 * @method Jquery addClass(...$args)
 * @method Jquery append(...$args)
 * @method Jquery appendTo(...$args)
 * @method Jquery attr(...$args)
 * @method Jquery bind(string $eventType, ...$args)
 * @method Jquery change(...$args)
 * @method Jquery checkbox(...$args)
 * @method Jquery click(...$args)
 * @method Jquery closest(...$args)
 * @method Jquery confirm(...$args)
 * @method Jquery css(...$args)
 * @method Jquery data(...$args)
 * @method Jquery dropdown(...$args)
 * @method Jquery fadeOut(...$args)
 * @method Jquery find(...$args)
 * @method Jquery first(...$args)
 * @method Jquery flatpickr(...$args)
 * @method Jquery focus(...$args)
 * @method Jquery get(...$args)
 * @method Jquery height(...$args)
 * @method Jquery hide(...$args)
 * @method Jquery html(...$args)
 * @method Jquery location(...$args)
 * @method Jquery off(...$args)
 * @method Jquery on(string $events, ...$args)
 * @method Jquery parent(...$args)
 * @method Jquery parents(...$args)
 * @method Jquery popup(...$args)
 * @method Jquery position(...$args)
 * @method Jquery progress(...$args)
 * @method Jquery prop(...$args)
 * @method Jquery rating(...$args)
 * @method Jquery ready(...$args)
 * @method Jquery reload(...$args)
 * @method Jquery removeAttr(...$args)
 * @method Jquery removeClass(...$args)
 * @method Jquery select(...$args)
 * @method Jquery serialize(...$args)
 * @method Jquery show(...$args)
 * @method Jquery submit(...$args)
 * @method Jquery tab(...$args)
 * @method Jquery text(...$args)
 * @method Jquery toast(...$args)
 * @method Jquery toggle(...$args)
 * @method Jquery toggleClass(...$args)
 * @method Jquery transition(...$args)
 * @method Jquery trigger(...$args)
 * @method Jquery val(...$args)
 *
 * For Fohn-plugin:
 * @method Jquery fohnAjaxec(...$args)
 * @method Jquery fohnReloadView(...$args)
 * @method Jquery fohnServerEvent(...$args)
 */
class Jquery extends JsChain
{
    public static string $jquery = 'jQuery';

    /**
     * Execute a Jquery function on page load or reload.
     * Ex: Jquery::onDocumentReady($view)->text('My new content').
     */
    public static function onDocumentReady(View $view): self
    {
        $jquery = self::withSelector('#' . $view->getIdAttribute());
        $view->appendJsAction($jquery);

        return $jquery;
    }

    public static function withView(View $view): self
    {
        return static::withSelector('#' . $view->getIdAttribute());
    }

    /**
     * Start chain with $.
     */
    public static function withSelf(): self
    {
        return new static(self::$jquery);
    }

    /**
     * Start chain with a string selector $("selector").
     */
    public static function withSelector(string $selector = null): self
    {
        return new static(self::$jquery, Js::string($selector));
    }

    /**
     * Start chain with variable $(variableName).
     * The variable should hold proper jQuery selector value.
     */
    public static function withVar(string $variableName): self
    {
        return new static(self::$jquery, Js::var($variableName));
    }

    /**
     * Start chain with $(this).
     */
    public static function withThis(): self
    {
        return new static(self::$jquery, Js::var('this'));
    }

    /**
     * Add a javascript event to a View.
     * render as $('#view_id')->on(event, selector, function() {}).
     */
    public static function addEventTo(View $view, string $event, string $selector = null, bool $prevent = false, bool $stop = false): JsFunction
    {
        $fn = JqFunction::anonymous();

        if ($prevent) {
            $fn->preventDefault();
        }

        if ($stop) {
            $fn->stopPropagation();
        }

        if ($selector) {
            self::onDocumentReady($view)->on($event, $selector, $fn);
        } else {
            self::onDocumentReady($view)->on($event, $fn);
        }

        return $fn;
    }

    /**
     * Add a Jquery ajax callback event to a View.
     */
    public static function jqCallback(View $view, string $event, \Closure $fn, array $requestPayload = [], string $selector = null): JsFunction
    {
        $callback = \Fohn\Ui\Callback\Jquery::addAbstractTo($view);
        $callback->onJqueryRequest(function (array $payload) use ($fn, $view): JsRenderInterface {
            return $fn(self::withSelector('#' . $view->getIdAttribute()), $payload);
        }, $requestPayload);

        return static::addEventTo($view, $event, $selector)->execute($callback);
    }
}
