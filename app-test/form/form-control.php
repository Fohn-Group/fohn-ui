<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\AppTest\Model\Country;
use Fohn\Ui\Component\Form;
use Fohn\Ui\Core\Utils;
use Fohn\Ui\Js\Jquery;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsFunction;
use Fohn\Ui\Js\JsToast;
use Fohn\Ui\Service\Atk\FormControlFactory;
use Fohn\Ui\Service\Data;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Tw;
use Fohn\Ui\View;

require_once __DIR__ . '/../init-ui.php';

/**
 * Return a Javascript function that will update text inside a view when executed.
 * Will render as: (newValue) => { $('#VIEW_ID')->text(newValue); }.
 */
$changeTextFn = function (View $view): JsFunction {
    return JsFunction::arrow([Js::var('newValue')])->execute(Jquery::withView($view)->text(Js::var('newValue')));
};

// Controls to add to form.
$controls = [
    new Form\Control\Line(['controlName' => 'name', 'caption' => 'Name:']),
    new Form\Control\Line(['controlName' => 'email', 'caption' => 'Email:', 'inputType' => 'email', 'placeholder' => 'email@domain.com']),
    (new Form\Control\Password(['controlName' => 'password', 'caption' => 'Password:']))->setValue('123456'),
    (new Form\Control\Number(['controlName' => 'number', 'caption' => 'Number:']))->setValue(3),
    (new Form\Control\Number(['controlName' => 'float', 'caption' => 'Float:', 'precision' => 3]))->setValue(3.045),
    (new Form\Control\Money(['controlName' => 'money', 'caption' => 'Money:']))->setValue(30.34),
    (new Form\Control\Select(['controlName' => 'select', 'caption' => 'Select:']))->setItems(['php' => 'PHP', 'js' => 'Javascript', 'java' => 'Java'])->setValue('php'),
    new Form\Control\Select(['controlName' => 'country', 'caption' => '\Select from Atk model :', 'placeholder' => 'Select a country']),
    (new Form\Control\Checkbox(['controlName' => 'check_php', 'caption' => 'PHP']))->setValue(true),
    new Form\Control\Checkbox(['controlName' => 'check_js', 'caption' => 'Javascript']),
    (new Form\Control\Radio(['controlName' => 'radio', 'caption' => 'Radio']))->setItems(['php' => 'PHP', 'js' => 'Javascript', 'java' => 'Java']),
    (new Form\Control\Calendar(['controlName' => 'date', 'caption' => 'Date', 'format' => Ui::getDisplayFormat('date')]))->setValue(new \DateTime()),
    (new Form\Control\Calendar(['controlName' => 'time', 'caption' => 'Time', 'format' => Ui::getDisplayFormat('time'), 'type' => 'time']))->setValue(new \DateTime()),
    (new Form\Control\Calendar(['controlName' => 'datetime', 'caption' => 'DateTime', 'format' => Ui::getDisplayFormat('datetime'), 'type' => 'datetime']))->setValue(new \DateTime()),
    (new Form\Control\Textarea(['controlName' => 'text', 'rows' => '4', 'caption' => 'Textarea']))->setValue(Utils::getLoremIpsum(12)),
    (new Form\Control\Range(['controlName' => 'range', 'caption' => 'Range (0-100)']))->setValue(50),
];

// Use a custom html template.
// Template using special tag name {$ctrl_CONTROL_NAME} will have the form control with name = CONTROL_NAME render in that region.
$form = Form::addTo(
    Ui::layout(),
    [
        'defaultLayout' => new Form\Layout\Standard(['template' => Ui::templateFromFile(
            __DIR__ . '/template/custom-form.html'
        )]),
    ]
);

$form->addControls($controls);

/** @var Form\Control\Select $countrySelect */
$countrySelect = $form->getControl('country');
$countrySelect->onItemsRequest(function (Form\Response\Items $response) {
    $response->setItems(FormControlFactory::getSelectItems(new Country(Data::db())));
});

/** @var Form\Control\Range $range */
$range = $form->getControl('range');
View::addBefore($range)
    ->appendTailwind('italic text-sm -mb-2')
    ->appendTailwind(Tw::textColor('secondary'))
    ->setTextContent('This range control use an onChange handler in order to update value in chip below. An optional debonce value is also apply to the onChange handler.');

$chip = View\Chip::addAfter($range, ['size' => '12', 'color' => 'secondary']);
$chip->appendTailwind('mx-auto');
$range->onChange($changeTextFn($chip->content), 500);

$form->onSubmit(function (Form $f) {
    return JsToast::success('Submit!');
});
