<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\AppTest\Model\FieldTest;
use Fohn\Ui\Component\Form;
use Fohn\Ui\Js\Jquery;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsFunction;
use Fohn\Ui\Js\JsToast;
use Fohn\Ui\Service\Data;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Tw;
use Fohn\Ui\View;

require_once __DIR__ . '/../init-ui.php';

View::addTo(Ui::layout(), ['htmlTag' => 'p'])
    ->setTextContent('Test adding control via an Atk model using specify record id.');
View::addTo(Ui::layout(), ['htmlTag' => 'p'])
    ->setTextContent('Record id must be supply when saving model using FormController.');

/**
 * Return a Javascript function that will update text inside a view.
 * Will render as: (newValue) => { $('#View_ID')->text(newValue); }.
 */
$getJsFunction = function (View $view): JsFunction {
    return JsFunction::arrow([Js::var('newValue')])->execute(Jquery::withView($view)->text(Js::var('newValue')));
};

$grid = View\GridLayout::addTo(Ui::layout(), ['columns' => 6, 'rows' => 1]);
$formContainer = View::addTo($grid)->appendTailwinds([Tw::gridCol('start', '2'), Tw::gridCol('span', '4')]);

$modelCtrl = Data::formModelCtrl(new FieldTest(Data::db()));
$recordId = (string) (new FieldTest(Data::db()))->loadAny()->getId();

$f = Form::addTo($formContainer);
$f->addControls($modelCtrl->factoryFormControls($recordId));
$nameCtrl = $f->getControl('first_name')
    ->onValidate(function (string $value) {
        $error = null;
        if ($value === 'JOHN') {
            $error = 'John is not allow here.';
        }

        return $error;
    })->onSetValue(function ($value) {
        return strtoupper($value);
    });

View::addAfter($nameCtrl)
    ->setTextContent('Test control onValidation and onSetValue Hook. Try entering John as value. Also the value save in db wil be in uppercase.')
    ->appendTailwinds([Tw::textColor('secondary'), 'italic']);

// @phpstan-ignore-next-line
$f->getControl('last_name')->placeholder = 'a placeholder';

$c = $f->addControl(new Form\Control\Range(['caption' => 'Range', 'controlName' => 'range']))
    ->setValue(23)
    ->onValidate(function (int $value) {
        if ($value < 20) {
            return 'Value is tool low.';
        }

        return null;
    });
$f->addControl(new Form\Control\Range(['caption' => 'Range', 'controlName' => 'range1']))->setValue(40);

$f->onSubmit(function (Form $f) use ($modelCtrl, $recordId) {
    if ($errors = $modelCtrl->saveModelUsingForm($recordId, $f->getControls())) {
        $f->addValidationErrors($errors);
    }

    return JsToast::success('Saved!', 'Record is now saved.');
});

// Adding standalone input with a debounce change handler.
$c = Form\Control\Input::addTo($formContainer, ['controlName' => 'std', 'caption' => 'Test'])->setValue('test');
$c->onChange(JsFunction::anonymous()
    ->execute(
        JsToast::notifyWithJs(
            Jquery::withSelector('input[name=\'std\']')->val()
        )
    ), 500);

$c1 = Form\Control\Input::addTo($formContainer, ['controlName' => 'std1'])->setValue('test');
$c1->onChange(JsFunction::anonymous()
    ->execute(
        JsToast::notifyWithJs(
            Jquery::withSelector('input[name=\'std1\']')->val()
        )
    ), 500);

Ui::viewDump($f, 'form');
Ui::viewDump($c, 'ctl');
