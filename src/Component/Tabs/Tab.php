<?php

declare(strict_types=1);
/**
 * Tab Component.
 * Tab view is a direct children of Tabs component.
 * It should be inserted within <fohn-tabs></fohn-tabs>.
 */

namespace Fohn\Ui\Component\Tabs;

use Fohn\Ui\Component\VueInterface;
use Fohn\Ui\Component\VueTrait;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsFunction;
use Fohn\Ui\View;

class Tab extends View implements VueInterface
{
    use VueTrait;

    private const FN_INIT_KEY = 'init';
    private const FN_SHOW_KEY = 'show';
    private const FN_HIDE_KEY = 'hide';

    public string $defaultTemplate = 'vue-component/tabs/tab.html';

    public string $tabStoreId;

    protected string $name = '';
    protected string $caption = '';
    protected bool $isDisabled = false;

    /** @var array<string, string> Tab properties that can be used in template within parent tabs property. */
    protected array $properties = [];

    /** @var array<string, JsFunction> JsFunction to execute when corresponding event occurs. */
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

    public function addProperty(string $key, string $value): self
    {
        $this->properties[$key] = $value;

        return $this;
    }

    /**
     * Execute javascript when tab is initialised (mount) by Vue.
     */
    public function jsOnInitTab(JsFunction $fn): JsFunction
    {
        $this->appendJsActiveHandlerFn(self::FN_INIT_KEY, $fn);

        return $fn;
    }

    /**
     * Execute javascript each time tab is show, i.e. become active.
     */
    public function jsOnShowTab(JsFunction $fn): JsFunction
    {
        $this->appendJsActiveHandlerFn(self::FN_SHOW_KEY, $fn);

        return $fn;
    }

    /**
     * Execute javascript each time tab is hide, i.e. become inactive.
     */
    public function jsOnHideTab(JsFunction $fn): JsFunction
    {
        $this->appendJsActiveHandlerFn(self::FN_HIDE_KEY, $fn);

        return $fn;
    }

    public function getProperties(): array
    {
        return array_merge($this->properties, ['name' => $this->getName(), 'caption' => $this->getCaption(), 'disabled' => $this->isDisabled()]);
    }

    /**
     * Add a Js Function to be executed when associate event occur.
     */
    private function appendJsActiveHandlerFn(string $key, JsFunction $fn): void
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
