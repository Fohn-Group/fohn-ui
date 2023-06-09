<?php

declare(strict_types=1);
/**
 * Utility Trait for Creating Vue Component.
 */

namespace Fohn\Ui\Component;

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
            }
        }

        return $isRoot;
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
        }

        return $this;
    }
}
