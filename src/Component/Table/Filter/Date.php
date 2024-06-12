<?php

declare(strict_types=1);
/**
 * Filter for type text.
 */

namespace Fohn\Ui\Component\Table\Filter;

use Fohn\Ui\Component\Table\Filter;
use Fohn\Ui\Component\Utils;

class Date implements FilterInterface
{
    protected string $type = 'date';
    private string $id;
    protected string $label;

    protected array $props = [];

    public function __construct(string $id, string $format = 'Y-m-d', string $label = null, array $props = [])
    {
        $this->id = $id;
        $this->label = $label ?? ucfirst($id);
        $this->props['name'] = $this->id;
        $this->props['config'] = Utils::getFlatPickrConfig($this->type, $format);

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
