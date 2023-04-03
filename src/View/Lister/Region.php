<?php

declare(strict_types=1);
/**
 * Lister Region.
 *
 * Lister Region is render as a list.
 * $items are key => value pair where the key correspond to a template tag name.
 */

namespace Fohn\Ui\View\Lister;

use Fohn\Ui\Core\HookFn;
use Fohn\Ui\Core\HookTrait;
use Fohn\Ui\HtmlTemplate;

class Region
{
    use HookTrait;

    public const HOOK_ITEM_RENDER = self::class . '@item_render';
    public HtmlTemplate $regionTemplate;

    /** @var array<array> */
    public array $items;

    public function __construct(array $items, HtmlTemplate $template)
    {
        $this->items = $items;
        $this->regionTemplate = $template;
    }

    public function appendItem(array $item): void
    {
        $this->items[] = $item;
    }

    public function renderToHtml(): string
    {
        $html = '';

        foreach ($this->items as $item) {
            $hookHtml = $this->callHook(self::HOOK_ITEM_RENDER, HookFn::withTypeFn(function ($fn, $args): string {
                return $fn(...$args);
            }, [$this->regionTemplate, $item]));

            $html .= $hookHtml ?: $this->regionTemplate->trySetMany($item)->renderToHtml();
        }

        return $html;
    }
}
