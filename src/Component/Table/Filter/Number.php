<?php

declare(strict_types=1);
/**
 * Filter for type text.
 */

namespace Fohn\Ui\Component\Table\Filter;

use Fohn\Ui\Component\Table\Filter;

class Number implements FilterInterface
{
    protected string $type = 'number';
    private string $id;
    protected string $label;

    protected array $props = [
        'type' => 'number',
    ];

    public function __construct(string $id, string $label = null, array $props = [])
    {
        $this->id = $id;
        $this->label = $label ?? ucfirst($id);
        $props['name'] = $this->id;
        $this->props = array_merge($this->props, $props);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDefinition(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'operatorType' => $this->type,
            'component' => [
                'name' => Filter::getComponentName($this->type),
                'props' => $this->props,
            ],
        ];
    }
}
