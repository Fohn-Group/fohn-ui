<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\AppTest\Model\Country;
use Fohn\Ui\Component\Form;
use Fohn\Ui\Js\JsToast;
use Fohn\Ui\Service\Atk\FormModelController;
use Fohn\Ui\Service\Data;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Tw;
use Fohn\Ui\View;

require_once __DIR__ . '/../init-ui.php';

$modelCtrl = new FormModelController(new Country(Data::db()));
$id = (string) $modelCtrl->getModel()->tryLoadAny()->get('id');

$form = Form::addTo(Ui::layout());

$form->onHook(Form::HOOK_BEFORE_CONTROL_ADD, function ($form, Form\Control $control, $layoutName) {
    if (get_class($control) === Form\Control\Input::class) {
        $control->setTemplate(Ui::templateFromFile(__DIR__ . '/template/custom-input.html'));
    }
});

$form->addControls($modelCtrl->factoryFormControls($id));
$form->onSubmit(function (Form $f) use ($modelCtrl, $id) {
    if ($errors = $modelCtrl->saveModelUsingForm($id, $f->getControls())) {
        $f->addValidationErrors($errors);
    }

    return JsToast::success('Saved!', 'Record is now saved.');
});

View::addAfter($form->getControl('iso3'))
    ->appendTailwind('italic text-sm my-2')
    ->appendTailwind(Tw::textColor('secondary'))
    ->setText('The ISO and ISO3 country codes are internationally recognized means of identifying countries (and their subdivisions) using a two-letter or three-letter combination.');
