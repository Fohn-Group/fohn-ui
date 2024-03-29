<?php

declare(strict_types=1);
/**
 * Utility Trait for Creating Vue Component.
 */

namespace Fohn\Ui\Component;

use Fohn\Ui\Core\Exception;
use Fohn\Ui\Js\JsChain;
use Fohn\Ui\Js\JsRenderInterface;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;

trait VueTrait
{
    /** Force Js vueService to create component. */
    public bool $forceRoot = false;

    /** Vue Pinia store id. Must be unique. */
    protected ?string $storeId = null;

    protected array $properties = [];

    protected array $events = [];

    /**
     * Bind a Vue v-on:event to a View via html attributes.
     * <button v-on:click="do something"></button>.
     */
    public static function bindVueEvent(View $view, string $eventName, string $event): void
    {
        if (!$view->getTemplate()->hasTag(View::ATTR_TEMPLATE_TAG)) {
            throw new Exception('Unable to bind Vue event. Template for View does not have the attributes tag.');
        }
        $view->appendHtmlAttribute('v-on:' . $eventName, $event);
    }

    /**
     * Bind a dynamic attribute to a view.
     * <div :disabled="{isLoading}"></div>.
     */
    public static function bindVueAttr(View $view, string $attr, string $value): void
    {
        if (!$view->getTemplate()->hasTag(View::ATTR_TEMPLATE_TAG)) {
            throw new Exception('Unable to bind Vue attribute. Template for View does not have the attributes tag.');
        }

        $view->appendHtmlAttribute(':' . $attr, $value);
    }

    protected function getDefaultSelector(): string
    {
        return '#' . $this->getIdAttribute();
    }

    /**
     * Generate a unique store id unless already set.
     * Uses script_name in order to avoid duplicate. This is critical for component that save it's store
     * state in LocalStorage.
     *
     * Providing a custom id will help when realtime update of a component is needed.
     * Ex: When updating table data after a pusher notification in js:
     *  fohn.vueService.getStore('myId').fetchItems().
     */
    protected function getPiniaStoreId(string $prefix = ''): string
    {
        if ($this->storeId) {
            return $this->storeId;
        }

        return $prefix . Ui::service()->factoryId(Ui::serverRequest()->getServerParams()['SCRIPT_NAME']) . '-' . $this->getIdAttribute();
    }

    public function isRootComponent(): bool
    {
        $isRoot = true;
        foreach ($this->getOwners() as $owner) {
            if ($owner instanceof VueInterface) {
                $isRoot = false;

                break;
            }
        }

        return $isRoot;
    }

    public function addProperty(string $property, JsRenderInterface $value, bool $isDynamic = true): self
    {
        $this->properties[($isDynamic ? ':' : '') . $property] = $value->jsRender();

        return $this;
    }

    protected function renderProperties(): void
    {
        $props = '';
        foreach ($this->properties as $k => $v) {
            $props .= $k . '="' . $v . '"';
        }

        $this->getTemplate()->tryDangerouslySetHtml('properties', $props);
    }

    public function addEvent(string $event, JsRenderInterface $declaration): void
    {
        $this->events['@' . $event] = $declaration->jsRender();
    }

    protected function renderEvents(): void
    {
        $props = '';
        foreach ($this->events as $k => $v) {
            $props .= $k . '="' . $v . '"';
        }

        $this->getTemplate()->tryDangerouslySetHtml('events', $props);
    }

    protected function jsGetStore(string $prefix): JsRenderInterface
    {
        // @phpstan-ignore-next-line
        return JsChain::withUiLibrary()->vueService->getStore($this->getPiniaStoreId($prefix));
    }

    /**
     * Create Vue root app component.
     * Only component at top most level is created by vueService except when specify with forceRoot property.
     * forceRoot is used when a component is created from a View reload.
     */
    protected function createVueApp(string $component, array $rootData, string $selector): self
    {
        if ($this->forceRoot || $this->isRootComponent()) {
            $chain = JsChain::withUiLibrary()->vueService->createVueApp($selector, $component, $rootData); // @phpstan-ignore-line

            $this->unshiftJsActions($chain);
            // make root component invisible until mounted by VueService.
            $this->appendTailwinds(['invisible', 'data-[v-app]:visible']);
        }

        return $this;
    }
}
