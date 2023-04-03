<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\AppTest\Model\Country;
use Fohn\Ui\Component\Form;
use Fohn\Ui\Component\Modal;
use Fohn\Ui\Component\Modal\AsDialog;
use Fohn\Ui\Core\Utils;
use Fohn\Ui\Js\Jquery;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsStatements;
use Fohn\Ui\Js\JsToast;
use Fohn\Ui\Service\Atk\FormModelController;
use Fohn\Ui\Service\Data;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;
use Fohn\Ui\View\Button;
use Fohn\Ui\View\Message;

require_once __DIR__ . '/../init-ui.php';

// / MODAL

$infoDialog = Modal::addTo(Ui::layout(), ['title' => 'Important!!!']);
$infoDialog->addCloseButton(new Button(['label' => 'Close', 'type' => 'outline', 'color' => 'info', 'size' => 'small']));

$msg = Message::addTo($infoDialog, ['title' => 'Note:', 'color' => 'error']);
$msg->addText(Utils::getLoremIpsum(20));

$btn = Button::addTo(Ui::layout(), ['label' => 'Display Info', 'color' => 'info', 'type' => 'outline']);
Jquery::addEventTo($btn, 'click')
    ->executes([
        $infoDialog->jsOpen(),
    ]);

// // AS DIALOG

$confirm = AsDialog::addTo(Ui::layout(), ['title' => 'Confirm this action', 'isClosable' => false]);

$confirm->addCallbackEvent('cancel', new Button(['label' => 'No', 'type' => 'outline', 'color' => 'error', 'size' => 'small']));
$confirm->onCallbackEvent('cancel', function (array $payload) use ($confirm) {
    return JsStatements::with([
        $confirm->jsClose(),
    ]);
});

$confirm->addCallbackEvent('confirm', new Button(['label' => 'Yes', 'type' => 'outline', 'color' => 'success', 'size' => 'small']));
$confirm->onCallbackEvent('confirm', function (array $payload) use ($confirm) {
    return JsStatements::with([
        JsToast::info('All goods!', 'Operation confirm.'),
        $confirm->jsClose(),
    ]);
});

$btn = Button::addTo(Ui::layout(), ['label' => 'Open Dialog', 'color' => 'info', 'type' => 'outline']);
Jquery::addEventTo($btn, 'click')
    ->executes([
        $confirm->jsOpen(['message' => 'Are you sure ?']),
    ]);

// / AS Form

$modelCtrl = new FormModelController(new Country(Data::db()));

$modalForm = Modal\AsForm::addTo(Ui::layout(), ['title' => 'Edit Country Record :']);

$form = $modalForm->addForm(new Form());
$form->addControls($modelCtrl->factoryFormControls(null));
$form->onControlsValueRequest(function ($id, Form\Response\Value $response) use ($modelCtrl) {
    $response->mergeValues($modelCtrl->getFormInputValue((string) $id));
});

$form->onSubmit(function ($f, $id) use ($modalForm, $modelCtrl) {
    if ($errors = $modelCtrl->saveModelUsingForm($id, $f->getControls())) {
        $f->addValidationErrors($errors);
    }

    return JsStatements::with(
        [
            JsToast::success('Saved!'),
            $modalForm->jsClose(),
        ]
    );
});

$bar = View::addTo(Ui::layout())->appendTailwinds(['inline-block, my-4']);
Button::addTo($bar, ['label' => 'Italy', 'color' => 'info', 'type' => 'outline', 'shape' => 'normal'])->appendHtmlAttribute('data-name', 'Italy');
Button::addTo($bar, ['label' => 'Norway', 'color' => 'info', 'type' => 'outline', 'shape' => 'normal'])->appendHtmlAttribute('data-name', 'Norway');
Button::addTo($bar, ['label' => 'Sweden', 'color' => 'info', 'type' => 'outline', 'shape' => 'normal'])->appendHtmlAttribute('data-name', 'Sweden');

Jquery::jqCallback($bar, 'click', function ($j, $payload) use ($modalForm, $modelCtrl) {
    $id = $modelCtrl->getModel()->tryLoadBy('name', $payload['name'])->get('id');

    return JsStatements::with($modalForm->jsOpenWithId(Js::var((string) $id)));
}, ['name' => Jquery::withThis()->data('name')], '.fohn-btn');

// / Dynamic

$modalDynamic = Modal::addTo(Ui::layout(), ['title' => 'Load on demand content.']);
$modalDynamic->addCloseButton(new Button(['label' => 'Close', 'type' => 'outline', 'color' => 'info', 'size' => 'small']));

$modalDynamic->onOpen(function ($modal) {
    $tw = ['first-line:uppercase', 'first-line:tracking-widest',
        'first-letter:text-7xl', 'first-letter:font-bold', 'first-letter:text-purple-700',
        'first-letter:mr-3', 'first-letter:float-left', ];

    View::addTo($modal)->setHtmlTag('p')->setText(Utils::getLoremIpsum(random_int(10, 100)))->appendTailwinds($tw);
});

$btn = Button::addTo(Ui::layout(), ['label' => 'Open Modal', 'color' => 'info', 'type' => 'outline']);
Jquery::addEventTo($btn, 'click')
    ->executes([
        $modalDynamic->jsOpen(),
    ]);
