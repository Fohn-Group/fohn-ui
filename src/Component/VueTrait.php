<?php

declare(strict_types=1);
/**
 * Utility Trait for Creating Vue Component.
 */

namespace Fohn\Ui\Component;

use Fohn\Ui\Js\JsChain;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;

trait VueTrait
{
    /** Force Js vueService to create component. */
    public bool $forceRoot = false;

    protected function getDefaultSelector(): string
    {
        return '#' . $this->getIdAttribute();
    }

    protected function getPiniaStoreId(string $prefix = ''): string
    {
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
