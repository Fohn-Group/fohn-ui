<?php

declare(strict_types=1);
/**
 * Filter for type time.
 */

namespace Fohn\Ui\Component\Table\Filter;

use Fohn\Ui\Component\Table\Filter;
use Fohn\Ui\Component\Utils;

class Time implements FilterInterface
{
    protected string $type = 'time';
    private string $id;
    protected string $label;

    protected array $props = [];

    public function __construct(string $id, string $format = 'H:i', string $label = null, array $props = [])
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
