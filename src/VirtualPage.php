<?php

declare(strict_types=1);

namespace Fohn\Ui;

use Fohn\Ui\Callback\Generic;
use Fohn\Ui\Js\JsChain;
use Fohn\Ui\Service\Ui;

/**
 * Virtual page render only when it is trigger.
 * Creating a VirtualPage require a Page view with a layout.
 * When page is requested via it's callback URL GET arguments;
 *  - The layout view inside the Page is clear of main content;
 *  - The layout view is set as parameter to the closure function where new content is added;
 *  - The page is render;.
 */
class VirtualPage extends AbstractView
{
    private static int $count = 0;
    protected ?Generic $cb = null;
    private Page $page;
    private string $trigger;

    final private function __construct(Page $page, string $trigger)
    {
        parent::__construct([]);
        // Generate unique trigger
        $trigger = $trigger ?: '__vp_' . ++self::$count;

        $this->page = $page;
        $this->trigger = $trigger;
    }

    /**
     * Create the VirtualPage.
     */
    public static function with(Page $page, string $trigger = ''): self
    {
        $vp = new static($page, $trigger);
        $vp->invokeInitRenderTree();

        return $vp;
    }

    protected function initRenderTree(): void
    {
        parent::initRenderTree();

        $this->cb = Generic::addAbstractTo($this->page, ['urlTrigger' => $this->trigger]);
    }

    public function getPage(): Page
    {
        return $this->page;
    }

    /**
     * Set Virtual page content when callback fire.
     */
    public function onPageRequest(\Closure $fx): void
    {
        $this->cb->onRequest(function () use ($fx) {
            $fx($this->page->getLayout());
            $this->terminate();
        });
    }

    public function getUrl(bool $includeParam = true): string
    {
        return $this->cb->getUrl($includeParam);
    }

    protected function terminate(): void
    {
        if ($this->cb->canTerminate()) {
            // @phpstan-ignore-next-line
            $this->page->getLayout()->appendJsAction(JsChain::withUiLibrary()->utils()->browser()->cleanHistory($this->getUrl(false)));
            Ui::app()->terminateHtml($this->page->outputHtml());
        }
    }
}
