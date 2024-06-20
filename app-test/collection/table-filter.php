<?php
/**
 * Create a Table Vue Component and display Country data
 * using an Atk4\Country model.
 *
 * Table contains two actions columns:
 *  - one for editing the select Country record using a Modal\AsForm component;
 *  - one for deleting the select Country record using a Modal\AsDialog component;
 */

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\AppTest\Model\Country;
use Fohn\Ui\Component\Table;
use Fohn\Ui\Service\Data;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;

require_once __DIR__ . '/../init-ui.php';

// View::addTo(Ui::layout())->appendTailwinds(['h-96']);

$ctrl = Data::tableModelCtrl(new Country(Data::db()));
$ctrl->setSearchFields(['name', 'iso']);

$grid = View::addTo(Ui::layout(), ['template' => Ui::templateFromFile(
    dirname(__DIR__) . '/templates/split-columns.html'
)]);

$table = Table::addTo($grid, ['keepSelectionAcrossPage' => true]);
$table->setCaption(AppTest::tableCaptionFactory('Countries'));

$table->addColumn('name', Table\Column\Generic::factory(['isSortable' => true]));
$table->addColumn('iso', Table\Column\Generic::factory(['isSortable' => true])->alignText('center'));
$table->addColumn('iso3', Table\Column\Generic::factory(['isSortable' => true])->alignText('center'));
$table->addColumn('numcode', Table\Column\Integer::factory());
$table->addColumn('phonecode', Table\Column\Integer::factory());

$filter = $table->addFilter(new Table\Filter());
$filter->addColumnFilter(new Table\Filter\Text('name'));
$filter->addColumnFilter(new Table\Filter\Text('iso'));
$filter->addColumnFilter(new Table\Filter\Text('iso3'));
$filter->addColumnFilter(new Table\Filter\Integer('numcode'));

// Response to an onDataRequest event from Table.
// Fill in Table\Result\Set $dataSet depending on $payload value.
$table->onDataRequest(static function (Table\Payload $payload, Table\Result\Set $result) use ($ctrl): void {
    $ctrl->setTableResultSet($payload, $result);
});

// Use Ui::viewDump to inspect a rendered template of a view using a console display like for debugging.
// Using url with dump args: /demos/collection/table-model.php?dump=table
Ui::viewDump($table, 'table');
