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
$form->addHeader(View::factory(['text' => 'Form Header']));
$form->addControls($modelCtrl->factoryFormControls($id));
$form->addFooter(View::factory(['text' => 'Form Footer']));

$iso = $form->getControl('iso');

$controls = $form->getControls();
$hasAControl = $form->hasControl('a_control');

$form->onSubmit(function (Form $f) use ($modelCtrl, $id) {
    if ($errors = $modelCtrl->saveModelUsingForm($id, $f->getControls())) {
        $f->addValidationErrors($errors);
    }

    return JsToast::success('Saved!');
});

View::addAfter($form->getControl('iso3'))
    ->appendTailwind('italic text-sm mt-2')
    ->appendTailwind(Tw::textColor('secondary'))
    ->setText('The ISO and ISO3 country codes are internationally recognized means of identifying countries (and their subdivisions) using a two-letter or three-letter combination.');

Ui::viewDump($form, 'form');
