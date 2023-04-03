<?php

declare(strict_types=1);
/**
 * An item in a group.
 */

namespace Fohn\Ui\Component\Navigation;

use Fohn\Ui\Core\InjectorTrait;

class Item
{
    use InjectorTrait;

    public string $name = '';
    public string $url = '';

    public function __construct(array $props = [])
    {
        $this->injectDefaults($props);
    }

//    public function getName(): string
//    {
//        return $this->name;
//    }

    public function getItemRootData(): array
    {
        return [
            'name' => $this->name,
            'url' => $this->url ?: '#',
        ];
    }
}
