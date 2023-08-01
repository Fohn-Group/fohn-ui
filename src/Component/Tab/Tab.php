<?php

declare(strict_types=1);
/**
 * Tab Component.
 */

namespace Fohn\Ui\Component\Tab;

use Fohn\Ui\Component\VueInterface;
use Fohn\Ui\Component\VueTrait;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsFunction;
use Fohn\Ui\View;

class Tab extends View implements VueInterface
{
    use VueTrait;

    protected const COMP_NAME = 'fohn-tab';
    private const FN_INIT_KEY = 'init';
    private const FN_SHOW_KEY = 'show';
    private const FN_HIDE_KEY = 'hide';


    public string $defaultTemplate = 'vue-component/tabs/tab.html';

    public string $tabStoreId;

    protected string $name = '';
    protected string $caption = '';
    protected bool $isDisabled = false;

    /** @var array<JsFunction> JsFunction to execute when this tab become active. */
    private array $onActiveHandlers = [];

    protected function initRenderTree(): void
    {
        parent::initRenderTree();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function disabled(): self
    {
        $this->isDisabled = true;

        return $this;
    }

    public function enable(): self
    {
        $this->isDisabled = false;

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->isDisabled;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setCaption(string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function jsOnInitTab(JsFunction $fn): JsFunction
    {
        $this->appendJsActiveHandlerFn($fn, self::FN_INIT_KEY);

        return $fn;
    }

    public function jsOnShowTab(JsFunction $fn): JsFunction
    {
        $this->appendJsActiveHandlerFn($fn, self::FN_SHOW_KEY);

        return $fn;
    }

    public function jsOnHideTab(JsFunction $fn): JsFunction
    {
        $this->appendJsActiveHandlerFn($fn, self::FN_HIDE_KEY);

        return $fn;
    }

    /**
     * Add a Js Function to be executed when this tab become active.
     * When $always is set to true, the Js function will run each time the tab become active,
     * otherwise, it will run only once.
     */
    private function appendJsActiveHandlerFn(JsFunction $fn, string $key): void
    {
        $this->onActiveHandlers[] = [$key => $fn];
    }


    protected function beforeHtmlRender(): void
    {
        $this->getTemplate()->trySetJs('tabName', Js::string($this->getName()));
        $this->getTemplate()->trySetJs('tabStoreId', Js::string($this->tabStoreId));
        $this->getTemplate()->trySetJs('onActiveHandlers', Js::array($this->onActiveHandlers));

        parent::beforeHtmlRender();
    }
}
