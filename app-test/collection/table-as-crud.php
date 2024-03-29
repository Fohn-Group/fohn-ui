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

use Atk4\Data\Model\Scope;
use Fohn\Ui\AppTest\Model\Country;
use Fohn\Ui\Component\Form;
use Fohn\Ui\Component\Modal;
use Fohn\Ui\Component\Table;
use Fohn\Ui\Js\Jquery;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsRenderInterface;
use Fohn\Ui\Js\JsStatements;
use Fohn\Ui\Js\JsToast;
use Fohn\Ui\Service\Atk\FormModelController;
use Fohn\Ui\Service\Data;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;
use Fohn\Ui\View\Button;

require_once __DIR__ . '/../init-ui.php';

$country = new Country(Data::db());
$modelCtrl = new FormModelController($country);

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

// ADD Country
$addDialog = Modal\AsForm::addTo($table, ['title' => 'Add Country:'], Table::TABLE_ACTION_REGION);
$addForm = $addDialog->addForm(Ui::factory(Form::class));
$addForm->addControls($modelCtrl->factoryFormControls(null));
// Response to form submit request.
$addForm->onSubmit(function (Form $f, ?string $id) use ($modelCtrl, $addDialog, $table): JsRenderInterface {
    if ($errors = $modelCtrl->saveModelUsingForm($id, $f->getControls())) {
        $f->addValidationErrors($errors);
    }

    return JsStatements::with(
        [
            JsToast::success('Success!', 'Record added.'),
            $table->jsFetchItems(),
            $addDialog->jsClose(),
        ]
    );
});

$btn = Button::addTo($table, ['label' => 'Add Country', 'color' => 'neutral'], Table::TABLE_ACTION_REGION);
Jquery::addEventTo($btn, 'click')->executes($addDialog->jsOpenWithId(null));

// Multiple Delete action
$actionDelete = (new Table\Action(['reloadTable' => true]))->setTrigger(Button::factory(['label' => 'Delete', 'color' => 'neutral']));
$actionMsg = new Table\Action\Messages();
$actionMsg->single = 'This action will delete 1 country. Are you sure?';
$actionMsg->multiple = 'This action will delete {#} countries. Are you sure?';

$actionDelete->addConfirmationDialog('Delete countries:', $actionMsg);
$table->addRowsAction($actionDelete)->onTrigger(function ($ids, $dialog) use ($modelCtrl) {
    // performs deletes on ids
    foreach ($ids as $id) {
        $modelCtrl->delete($id);
    }

    return JsStatements::with([JsToast::success('Delete Action! ' . implode(' / ', $ids)), $dialog->jsClose()]);
});

// Multiple process action
$actionProcess = (new Table\Action(['keepSelection' => true]))->setTrigger(Button::factory(['label' => 'Process', 'color' => 'neutral']));
$table->addRowsAction($actionProcess)->onTrigger(function ($ids) {
    sleep(2);

    return JsStatements::with([JsToast::success('Process Action! ' . implode(' / ', $ids))]);
});

// Edit and delete Dialog
$editDialog = Modal\AsForm::addTo(Ui::layout(), ['title' => 'Edit Country']);
$deleteDialog = Modal\AsDialog::addTo(Ui::layout(), ['title' => 'Confirm country deletion:']);

// Add form to edit dialog
$editForm = $editDialog->addForm(Ui::factory(Form::class));
$editForm->addControls($modelCtrl->factoryFormControls(null));

// Response to form request value callback using $ctrl.
$editForm->onControlsValueRequest(function ($id, Form\Response\Value $response) use ($modelCtrl) {
    $response->mergeValues($modelCtrl->getFormInputValue((string) $id));
});

// Response to form submit request.
$editForm->onSubmit(function (Form $f, ?string $id) use ($modelCtrl, $editDialog, $table): JsRenderInterface {
    if ($errors = $modelCtrl->saveModelUsingForm($id, $f->getControls())) {
        $f->addValidationErrors($errors);
    }

    return JsStatements::with(
        [
            JsToast::success('Success!', 'Record saved.'),
            $table->jsUpdateRow($id, $f->getControlValues()),
            $editDialog->jsClose(),
        ]
    );
});

// Add Edit action column to table using a click event (default).
// The method return a Js arrow function using the cell property value as argument. ex: (cell) => {};
$editActionFn = $table->addActionColumn('action', 'edit', AppTest::tableBtnFactory('bi bi-pencil-fill'));
// Add statements to the function. Function is execute on event set in addActionColumn.
$editActionFn->executes($editDialog->jsOpenWithId(Js::var('cell.id')));

// Add Delete action colmn to table.
$deleteActionFn = $table->addActionColumn('action', 'delete', AppTest::tableBtnFactory('bi bi-x-circle-fill', 'error'));

// Get the current field 'name' value using a javascript expression.
// Create a user msg using a Js expression.
$msg = Js::from("'Are you sure you want to delete ' + {{countryName}} + ' ?'", ['countryName' => $table->jsGetCellValue('name')]);
// Open delete dialog on click.
$deleteActionFn->executes([$deleteDialog->jsOpen(['message' => $msg, 'payload' => ['id' => Js::var('cell.id')]])]);

// Add callback event to Dialog when user confirm the action.
$deleteDialog->addCallbackEvent('confirm', new Button(['label' => 'Delete', 'color' => 'info']));
$deleteDialog->onCallbackEvent('confirm', function ($payload) use ($deleteDialog, $table, $modelCtrl) {
    // Delete record in db. Record id is set in $payload['id']
    $modelCtrl->delete($payload['id']);

    return JsStatements::with([
        JsToast::success('Delete' . $payload['id']),
        $deleteDialog->jsClose(),
        $table->jsDeleteRow($payload[$table->idColumnName]),
    ]);
});

// Response to an onDataRequest event from Table.
// Fill in Table\Result\Set $dataSet depending on $payload value.
$table->onDataRequest(function (Table\Payload $payload, Table\Result\Set $result) use ($country): void {
    $searchFields = ['name'];
    if ($payload->sortColumn) {
        $country->setOrder($payload->sortColumn, $payload->sortDirection);
    }
    if ($payload->searchQuery) {
        $scope = Scope::createOr();
        foreach ($country->getFields() as $field) {
            if (in_array($field->shortName, $searchFields, true)) {
                $scope->addCondition($field, 'like', '%' . $payload->searchQuery . '%');
            }
        }
        $country->addCondition($scope);
    }
    $country->setLimit($payload->ipp, ($payload->page - 1) * $payload->ipp);

    $result->totalItems = (int) $country->action('count')->getOne();
    $result->dataSet = $country->export();
});

// Use Ui::viewDump to inspect a rendered template of a view using a console display like for debugging.
// Using url with dump args: /demos/collection/table-model.php?dump=table
Ui::viewDump($table, 'table');
// Using url with dump args: /demos/collection/table-model.php?dump=edit
Ui::viewDump($editDialog, 'edit');
// Using url with dump args: /demos/collection/table-model.php?dump=delete
Ui::viewDump($deleteDialog, 'delete');
