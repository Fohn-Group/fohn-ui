<?php

declare(strict_types=1);

namespace Fohn\Ui\Js;

use Fohn\Ui\App;
use Fohn\Ui\Callback;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;

/**
 * Wrapper class for generating proper javascript for view reloading.
 * Will generate javascript needed in order to be able to reload the view.
 * Usage: Jquery::addEventTo($button, 'click')->execute(JsReload::view($b));.
 */
class JsReload implements JsRenderInterface
{
    protected Callback\JqReload $cb;

    /** Args added to the url. */
    private array $queryArgument;

    final private function __construct(View $view, array $queryArguments = [])
    {
        $this->queryArgument = $queryArguments;
        $this->cb = Callback\JqReload::addAbstractTo($view, ['requestPayload' => $queryArguments]);

        Ui::app()->onHooks(App::HOOKS_BEFORE_OUTPUT, function () {
            $this->cb->onJqueryRequest(function () {}, $this->queryArgument);
        });
    }

    public static function view(View $view, array $queryArguments = []): self
    {
        return new static($view, $queryArguments);
    }

    public function includeStorage(): self
    {
        $this->cb->includeStorage();

        return $this;
    }

    public function afterSuccess(JsRenderInterface $expression): self
    {
        $this->cb->setAfterSuccess($expression);

        return $this;
    }

    public function setConfig(array $config): self
    {
        $this->cb->apiConfig = $config;

        return $this;
    }

    public function jsRender(): string
    {
        return $this->cb->jsRender();
    }
}
