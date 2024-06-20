<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\AppTest\Model\Employees;
use Fohn\Ui\Component\Table;
use Fohn\Ui\Js\JsStatements;
use Fohn\Ui\Js\JsToast;
use Fohn\Ui\Service\Data;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Tw;
use Fohn\Ui\View\Button;

require_once __DIR__ . '/../init-ui.php';

$modelCtrl = Data::tableModelCtrl(new Employees(Data::db()));
$modelCtrl->setSearchFields(['firt_name', 'last_name']);

$table = Table::addTo(Ui::layout(), ['keepSelectionAcrossPage' => true]);
$table->setCaption(AppTest::tableCaptionFactory('Employees'));
$table->getTableTw()->merge([Tw::textSize('sm')]);

// Multiple process action
$actionProcess = (new Table\Action(['keepSelection' => true]))->setTrigger(Button::factory(['label' => 'Process', 'color' => 'neutral']));
$table->addRowsAction($actionProcess)->onTrigger(function ($ids) {
    $count = count($ids);

    return JsStatements::with([JsToast::success("Process Action on {$count} employee(s)")]);
});

// @phpstan-ignore-next-line
$table->addColumns($modelCtrl->getModel()->getTableColumns());
// @phpstan-ignore-next-line
$table->filterColumns($modelCtrl->getModel()->getTableFilters());

$table->onDataRequest(function (Table\Payload $payload, Table\Result\Set $result) use ($modelCtrl): void {
    $modelCtrl->setTableResultSet($payload, $result);
});
