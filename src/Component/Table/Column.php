<?php

declare(strict_types=1);

namespace Fohn\Ui\Component\Table;

use Fohn\Ui\Component\Table;
use Fohn\Ui\Core\HookFn;
use Fohn\Ui\Core\HookTrait;
use Fohn\Ui\Tailwind\Tw;
use Fohn\Ui\View;

/**
 * Default Table column.
 */
abstract class Column extends View
{
    use HookTrait;

    public const TEMPLATE_HEADER_REGION = 'Header';
    public const TEMPLATE_CELL_REGION = 'Cell';
    public const TEMPLATE_COLUMN_NAME_TAG = 'columnName';

    public array $headerTws = [
        'text-center',
        'p-2',
    ];

    public array $cellTws = [
        'p-2',
    ];

    /** Function executing this hook should return a Tw object. */
    public const HOOK_CELL_TW = self::class . '@cell:tw';
    public const HOOK_FORMAT_AS = self::class . '@format';

    protected string $caption = '';
    protected bool $isReadonly = false;

    /** What to display when column real value is set to null. */
    protected string $nullValue = '';

    /** The name of this control. */
    protected string $columnName;

    protected bool $isSortable = false;

    public ?Header $columnHeader = null;
    public array $columnHeaderSeed = [Header::class];

    /**
     * @param mixed      $value
     * @param string|int $id
     */
    abstract public function getDisplayValue($value, $id): string;

    public function setColumnName(string $name): self
    {
        $this->columnName = $name;

        return $this;
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }

    public function isSortable(): bool
    {
        return $this->isSortable;
    }

    public function alignText(string $alignment): self
    {
        $css = [
            'left' => 'text-left',
            'center' => 'text-center',
            'right' => 'text-right',
        ];

        $this->appendTailwind($css[$alignment]);

        return $this;
    }

    /**
     * Supply a Closure function in order to transform the original cell value.
     * Closure function will receive the column instance with the column value as arguments.
     * Closure function must return a string.
     */
    public function formatValue(\Closure $fx): self
    {
        $this->onHook(self::HOOK_FORMAT_AS, $fx);

        return $this;
    }

    /**
     * Supply a Closure function in order to apply css class to rendered <td> tag.
     * Closure function will receive the column value as argument.
     * Closure function must return a Tw instance.
     */
    public function applyCssCell(\Closure $fx): self
    {
        $this->onHook(self::HOOK_CELL_TW, $fx);

        return $this;
    }

    /**
     * @param mixed $value
     */
    protected function getFormatterValue($value): ?string
    {
        return $this->callHook(self::HOOK_FORMAT_AS, HookFn::withTypeFn(function ($fn, $args): string {
            return $fn(...$args);
        }, [$this, $value]));
    }

    public function getCaption(): string
    {
        if (!$this->caption) {
            $this->caption = ucfirst($this->columnName);
        }

        return $this->caption;
    }

    public function renderInTableTemplate(Table $table): void
    {
        if ($this->columnHeader) {
            $this->columnHeader->getTemplate()->set(self::TEMPLATE_COLUMN_NAME_TAG, $this->columnName);
            $this->columnHeader->getTemplate()->trySet('headerTws', Tw::from($this->headerTws)->toString());
            $table->getTemplate()->dangerouslyAppendHtml(Table::TEMPLATE_HEADERS_TAG, $this->columnHeader->getHtml());
        }

        $this->getTemplate()->set(self::TEMPLATE_COLUMN_NAME_TAG, $this->columnName);
        $this->getTemplate()->trySet('cellTws', Tw::from(array_merge($this->getTws(), $this->cellTws))->toString());

        $table->getTemplate()->dangerouslyAppendHtml(Table::TEMPLATE_CELLS_TAG, $this->getHtml());
    }
}
