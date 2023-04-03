<?php

declare(strict_types=1);
/**
 *   Create Html List.
 *
 *  First look for specific template tag name ending in order to define region.
 *  Each region is then clone into its own html template.
 *  At render, each item tags are rendered within its corresponding region, creating the final html output.
 *  Finally, the final html output replace the template main region.
 *
 *  It is possible to supply your own rendering value for each region name using
 *  Lister::onRegionRender(string $regionName, Closure $fn) method.
 *  The closure function will receive the region template as well as tags to be rendered.
 */

namespace Fohn\Ui\View;

use Fohn\Ui\Core\Exception;
use Fohn\Ui\HtmlTemplate;
use Fohn\Ui\View;

class Lister extends View
{
    protected const HOOK_REGION_RENDER = self::class . '@region_render';

    /** @var array<string, View\Lister\Region> Repeatable region name of this template. */
    public array $repeatableRegions = [];

    public function setRegionItems(string $regionName, array $items, HtmlTemplate $template): self
    {
        $this->assertRegionExist($regionName);

        $this->repeatableRegions[$regionName] = new View\Lister\Region($items, $template);

        return $this;
    }

    public function appendRegionItem(string $regionName, array $item): self
    {
        $this->repeatableRegions[$regionName]->appendItem($item);

        return $this;
    }

    protected function getRegion(string $regionName): View\Lister\Region
    {
        return $this->repeatableRegions[$regionName];
    }

    private function assertRegionExist(string $name): void
    {
        if (!$this->getTemplate()->hasTag($name)) {
            throw (new Exception('Unable to repeat Items because region does not exist in template'))
                ->addMoreInfo('region', $name);
        }
    }

    /**
     *  Define specific region template rendering.
     */
    public function onItemRender(string $regionName, \Closure $fn): void
    {
        $this->getRegion($regionName)->onHook(View\Lister\Region::HOOK_ITEM_RENDER, function (HtmlTemplate $template, array $tags) use ($fn) {
            return $fn($template, $tags);
        });
    }

    public function beforeHtmlRender(): void
    {
        foreach ($this->repeatableRegions as $key => $region) {
            $this->getTemplate()->tryDangerouslySetHtml($key, $region->renderToHtml());
        }

        parent::beforeHtmlRender();
    }
}
