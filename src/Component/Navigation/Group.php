<?php

declare(strict_types=1);
/**
 * A Navigation group.
 */

namespace Fohn\Ui\Component\Navigation;

use Fohn\Ui\Core\InjectorTrait;

class Group
{
    use InjectorTrait;

    protected string $name;
    public string $url = '';
    public string $icon = '';

    /** @var Item[] */
    public array $items = [];

    public function __construct(array $props = [])
    {
        $this->injectDefaults($props);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGroupRootData(): array
    {
        $props = [
            'name' => $this->getName(),
            'url' => $this->url,
            'icon' => $this->icon,
            'items' => $this->getItemsRootData(),
        ];

        return $props;
    }

    private function getItemsRootData(): array
    {
        $items = [];

        foreach ($this->items as $item) {
            // @var Item $item
            $items[] = $item->getItemRootData();
        }

        return $items;
    }
}
