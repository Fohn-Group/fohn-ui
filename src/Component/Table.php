<?php

declare(strict_types=1);
/**
 * Table Vue Component.
 *
 * Allow action buttons. (create, delete, update)
 *  - Action performed via callback.
 * Allow table search, which search data source.
 * Allow table filter, which filter data source.
 * Allow pagination.
 */

namespace Fohn\Ui\Component;

use Fohn\Ui\Callback\Data;
use Fohn\Ui\Component\Table\Column;
use Fohn\Ui\Component\Table\Header;
use Fohn\Ui\Component\Table\Payload;
use Fohn\Ui\Core\Exception;
use Fohn\Ui\Core\HookFn;
use Fohn\Ui\Core\HookTrait;
use Fohn\Ui\Js\Jquery;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsFunction;
use Fohn\Ui\Js\JsRenderInterface;
use Fohn\Ui\Js\Type\ArrayLiteral;
use Fohn\Ui\Js\Type\Integer;
use Fohn\Ui\Js\Type\ObjectLiteral;
use Fohn\Ui\Js\Type\Variable;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Tw;
use Fohn\Ui\View;

class Table extends View implements VueInterface
{
    use HookTrait;
    use VueTrait;

    public const CELL_PROP_NAME = 'cell';

    public string $defaultTemplate = 'vue-component/table.html';
    protected const HOOKS_DATA_REQUEST = self::class . '@data_request';
    protected const HOOKS_QUERY_REQUEST = self::class . '@query_request';
    private const COMP_NAME = 'fohn-table';

    private const PINIA_PREFIX = '__table_';
    public const TEMPLATE_HEADERS_TAG = 'headers';
    public const TEMPLATE_CELLS_TAG = 'cells';
    public const TEMPLATE_SEARCH_REGION = 'TableSearch';
    public const TEMPLATE_HEADER_REGION = 'TableHeaders';
    public const TEMPLATE_PAGINATOR_REGION = 'Paginator';

    /** Function executing this hook should return a Tw object. */
    public const HOOK_ROW_TW = self::class . '@hook_row:tw';

    public bool $hasColumnsHeader = true;
    public bool $hasTableSearch = true;

    /** Will keep table state on refresh. */
    public bool $keepTableState = true;

    public bool $hasPaginator = true;
    public int $paginatorLimit = 5;
    public int $paginatorItemsPerPage = 10;

    /** @var Column[] */
    private array $columns = [];

    public string $idColumnName = 'id';

    /** @var array<string, JsFunction> An array of table action */
    protected array $actions = [];

    protected ?Data $tableDataCb = null;

    protected array $tableTws = [
        'w-full',
        'border',
        'border-collapse',
        'table-auto',
    ];

    protected array $rowTws = [
        'border',
        'border-collapse',
    ];

    /** The data array */
    protected ?array $data = null;

    public function setCaption(View $caption): self
    {
        $this->addView($caption, 'caption');

        return $this;
    }

    /**
     * Supply a Closure function in order to apply css class to rendered on <tr> tag.
     * Closure function will receive the id value and a row object instance as arguments.
     * Closure function must return a Tw instance.
     * Closure function is called for every row in table.
     */
    public function applyCssRow(\Closure $fx): self
    {
        $this->onHook(self::HOOK_ROW_TW, $fx);

        return $this;
    }

    public function addColumn(string $name, Column $column): Column
    {
        $this->assertColumnIsUnique($name);
        $column->setColumnName($name);
        if ($this->hasColumnsHeader && $column->columnHeader === null) {
            $column->columnHeader = Table\Header::factoryFromSeed($column->columnHeaderSeed);
        }
        $this->columns[$name] = $column;

        return $column;
    }

    public function addColumns(array $columns): self
    {
        foreach ($columns as $name => $column) {
            $this->addColumn($name, $column);
        }

        return $this;
    }

    public function hasColumn(string $name): bool
    {
        return isset($this->columns[$name]);
    }

    public function jsGetCellValue(string $colName): JsRenderInterface
    {
        $idVar = self::CELL_PROP_NAME . '.' . $this->idColumnName;

        return $this->jsGetStore(self::PINIA_PREFIX)->getCellValue(Js::var($idVar), Js::string($colName));
    }

    public function addActionColumn(string $columnName, string $actionName, View\Button $button, Header $header = null, string $eventName = 'click'): JsFunction
    {
        if (!$this->hasColumn($columnName)) {
            if (!$header) {
                $header = Header::factory(
                    ['template' => Ui::templateFromFile('vue-component/table/column/header-empty.html')]
                );
            }
            $this->addColumn(
                $columnName,
                Column\Action::factory(
                    [
                        'columnHeader' => $header,
                    ]
                )->alignText('center')
            );
        }
        $button->setViewName($actionName);
        Ui::bindVueEvent($button, $eventName, "executeAction('{$actionName}', cell)");
        $column = $this->getTableColumn($columnName);
        $column->addView($button);

        $this->actions[$actionName] = JsFunction::arrow([Variable::set('cell')]);

        return $this->actions[$actionName];
    }

    public function setColumJqueryEvents(string $columnName, string $eventName, array $statements): self
    {
        Jquery::addEventTo($this, $eventName, "[data-cell-name='{$columnName}']")->executes(
            $statements
        );

        return $this;
    }

    public function setActionJqueryEvents(string $actionName, string $eventName, array $statements): self
    {
        Jquery::addEventTo($this, $eventName, "[data-ui-name='{$actionName}']")->executes(
            $statements
        );

        return $this;
    }

    public function onDataRequest(\Closure $fx): self
    {
        $this->initDataRequest();
        $this->onHooks(self::HOOKS_DATA_REQUEST, $fx);

        return $this;
    }

    /**
     * Update a table row with Javascript.
     * $row array should contains key => value pair.
     */
    public function jsUpdateRow(?string $id, array $row): JsRenderInterface
    {
        return $this->jsGetStore(self::PINIA_PREFIX)->updateRow($id, $row);
    }

    public function jsDeleteRow(string $id): JsRenderInterface
    {
        return $this->jsGetStore(self::PINIA_PREFIX)->deleteRow($id);
    }

    public function jsDataRequest(array $args = []): JsRenderInterface
    {
        // @phpstan-ignore-next-line
        return $this->jsGetStore(self::PINIA_PREFIX)->fetchItems(Js::object($args));
    }

    public function getTableColumn(string $name): Column
    {
        return $this->columns[$name];
    }

    protected function initDataRequest(): void
    {
        if (!$this->tableDataCb) {
            $this->tableDataCb = Data::addAbstractTo($this);
        }
    }

    protected function beforeHtmlRender(): void
    {
        $this->outputDataOnRequest();
        if (!$this->hasColumnsHeader) {
            $this->getTemplate()->del(self::TEMPLATE_HEADER_REGION);
        }
        if (!$this->hasTableSearch) {
            $this->getTemplate()->del(self::TEMPLATE_SEARCH_REGION);
        }
        if (!$this->hasPaginator) {
            $this->getTemplate()->del(self::TEMPLATE_PAGINATOR_REGION);
        } else {
            $this->getTemplate()->trySet('pagesLimit', (string) $this->paginatorLimit);
        }

        $this->getTemplate()->trySet('tableTws', Tw::from($this->tableTws)->toString());
        $this->getTemplate()->trySet('rowTws', Tw::from($this->rowTws)->toString());
        $this->renderTableProps();

        foreach ($this->columns as $column) {
            $column->renderInTableTemplate($this);
        }

        $this->createVueApp(self::COMP_NAME, [], $this->getDefaultSelector());

        parent::beforeHtmlRender();
    }

    /**
     * When call this method will output table data as json if
     * tableDataCallback is trigger.
     */
    protected function outputDataOnRequest(): void
    {
        $this->tableDataCb->onDataRequest(function (array $payload = []): array {
            $resultSet = new Table\Result\Set($this);
            $this->callHooks(self::HOOKS_DATA_REQUEST, HookFn::withVoid([new Payload($payload), $resultSet]));

            return $resultSet->outputData($this->columns, $this->idColumnName);
        });
    }

    private function assertColumnIsUnique(string $name): void
    {
        if (array_key_exists($name, $this->columns)) {
            throw (new Exception('This column is already set.'))
                ->addMoreInfo('column name: ', $name);
        }
    }

    private function renderTableProps(): void
    {
        $this->getTemplate()->set('storeId', $this->getPiniaStoreId(self::PINIA_PREFIX));
        $this->getTemplate()->set('dataUrl', $this->tableDataCb->getUrl());
        $this->getTemplate()->setJs('keepTableState', Js::boolean($this->keepTableState));
        $this->getTemplate()->setJs('columns', ArrayLiteral::set($this->getColumnsDefinition()));
        $this->getTemplate()->setJs('itemsPerPage', Integer::set($this->paginatorItemsPerPage));
        $this->getTemplate()->setJs('tableActions', ObjectLiteral::set($this->actions));
    }

    private function getColumnsDefinition(): array
    {
        $columns = [];
        // create header row from tableColumns
        foreach ($this->columns as $name => $column) {
            $columns[] = [
                'name' => $column->getColumnName(),
                'label' => $column->getCaption(),
                'isSortable' => $column->isSortable(),
            ];
        }

        return $columns;
    }
}
