<?php

declare(strict_types=1);
/**
 * JsRenderInterface wrapper for toastService.
 * toastService use Vue-Toastification packages in order to display toast content.
 * Notify and NotifyJs $options parameter can be any of Vue-toastification options object.
 *
 *  Here are some of the most important ones:
 *  - type: either, default, success, info, warning or error. The default one being: 'default';
 *  - timeout: duration in milliseconds, default being 5000. Setting this value to 0 will display toast forever until
 *              user close it.
 *  - position: either top-right, top-center, top-left, bottom-right, bottom-center or bottom-left.
 *
 * More options here: https://github.com/Maronato/vue-toastification#toast-options-object
 */

namespace Fohn\Ui\Js;

use Fohn\Ui\Js\Type\Boolean;
use Fohn\Ui\Js\Type\ObjectLiteral;
use Fohn\Ui\Js\Type\StringLiteral;

class JsToast
{
    /** The Javascript package toast service. */
    protected JsChain $toastService;

    final private function __construct()
    {
        // @phpstan-ignore-next-line
        $this->toastService = JsChain::withUiLibrary()->toastService;
    }

    /**
     * Return a JsRenderInterface in order to display Toast notification
     * using Php string and array for Toast options.
     * Example: when returning Js after a form submit.
     * $form->onSubmit(function($f) {
     *   // ...save form.
     *   return JsToast::notify('Save');
     * });.
     */
    public static function notify(string $title, string $message = '', array $options = [], bool $sanitize = true): JsRenderInterface
    {
        return static::notifyWithJs(StringLiteral::set($title), StringLiteral::set($message), ObjectLiteral::set($options), $sanitize);
    }

    public static function success(string $title, string $message = '', array $options = [], bool $sanitize = true): JsRenderInterface
    {
        $options = array_merge($options, ['type' => 'success']);

        return static::notify($title, $message, $options, $sanitize);
    }

    public static function error(string $title, string $message = '', array $options = [], bool $sanitize = true): JsRenderInterface
    {
        $options = array_merge($options, ['type' => 'error']);

        return static::notify($title, $message, $options, $sanitize);
    }

    public static function warning(string $title, string $message = '', array $options = [], bool $sanitize = true): JsRenderInterface
    {
        $options = array_merge($options, ['type' => 'warning']);

        return static::notify($title, $message, $options, $sanitize);
    }

    public static function info(string $title, string $message = '', array $options = [], bool $sanitize = true): JsRenderInterface
    {
        $options = array_merge($options, ['type' => 'info']);

        return static::notify($title, $message, $options, $sanitize);
    }

    /**
     * Return a JsRenderInterface in order to display Toast notification
     * using Javascript expression.
     * For example, setting the title of the notification from a javascript expression.
     * Jquery::addEventTo($btn, 'click')->execute(JsToast::notifyWithJs(Jquery::withThis()->text()));
     *   -  Render as:
     *   $('#btn').on('click',function (e) {
     *       fohn.toastService.notify($(this).text(),{});
     *   });.
     */
    public static function notifyWithJs(JsRenderInterface $title, JsRenderInterface $message = null, JsRenderInterface $options = null, bool $sanitize = true): JsRenderInterface
    {
        // / @phpstan-ignore-next-line
        return (new static())->toastService->notify($title, $message ?? StringLiteral::set(''), $options ?? ObjectLiteral::set([]), Boolean::set($sanitize));
    }
}
