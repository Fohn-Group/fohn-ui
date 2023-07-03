<?php

declare(strict_types=1);

namespace Fohn\Ui\Component\Table\Result;

use Fohn\Ui\Component\Table;
use Fohn\Ui\Component\Table\Column;
use Fohn\Ui\Core\HookFn;
use Fohn\Ui\Core\HookTrait;
use Fohn\Ui\Js\JsStatements;

/**
 * Table data result set.
 */
class Set
{
    use HookTrait;

    private Table $table;

    public int $totalItems;
    /** @var array[] */
    public array $dataSet = [];

    /** Javascript to execute with the results set. */
    public JsStatements $jsStatements;

    public function __construct(Table $table, array $dataSet = [], int $totalItems = 0)
    {
        $this->table = $table;
        $this->dataSet = $dataSet;
        $this->totalItems = $totalItems;
        $this->jsStatements = JsStatements::with([]);
    }

    public function outputData(array $columns, string $idColumnName): array
    {
        $results = [];
        $results['totalItems'] = $this->totalItems;

        foreach ($this->dataSet as $k => $row) {
            $id = $row[$idColumnName] ?? $k;
            // call hook function with row id and row array cast as an object as params. Callback must return a Tw object.
            // Column value in callback can be access using $row->{columnName}
            $rowTws = $this->table->callHook(Table::HOOK_ROW_TW, HookFn::withTw([(string) $id, (object) $row]));
            $cells = [];
            foreach ($columns as $name => $column) {
                $value = null;
                $cellTws = null;
                if (!$column instanceof Column\ActionInterface) {
                    $value = $column->getDisplayValue($row[$name], $id);
                    // call hook function with cell value as params. Callback must return a Tw object.
                    $cellTws = $column->callHook(Column::HOOK_CELL_TW, HookFn::withTw([$row[$name]]));
                }

                $cells[$name] = [
                    'id' => (string) $id,
                    'name' => $column->getColumnName(),
                    'value' => $value,
                    'css' => $cellTws !== null ? $cellTws->toString() : '',
                ];
            }
            $results['rows'][] = ['id' => (string) $id, 'cells' => $cells, 'css' => $rowTws->toString()];
        }
        $results['jsRendered'] = $this->jsStatements->jsRender();

        return $results;
    }
}
