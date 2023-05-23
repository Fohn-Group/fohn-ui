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

    /**
     * Unique store id. If not provided, then a unique id will be generated.
     * Providing an id help when realtime update is needed. This way you can
     * use the known id in order to get the component store and call function in it within your javascript.
     * Ex: when updating table data after a pusher notification in js:
     *  fohn.vueService.getStore('myId').fetchItems().
     */
    protected ?string $storeId = null;

    protected function getDefaultSelector(): string
    {
        return '#' . $this->getIdAttribute();
    }

    /**
     * Generate a unique store id. Uses script_name in order
     * to avoid duplicate. If a page is duplicate, using the same views,
     * then it is possible to have the same store id.
     * This is critical for component that save it's store state in LocalStorage.
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
