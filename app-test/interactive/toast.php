<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\Component\Form;
use Fohn\Ui\Core\Utils;
use Fohn\Ui\Js\Jquery;
use Fohn\Ui\Js\JsToast;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;
use Fohn\Ui\View\Button;

require_once __DIR__ . '/../init-ui.php';

Ui::layout()->appendJsAction(JsToast::notify('This toast is display on page load.'));

$info = View::addTo(Ui::layout(), ['text' => 'Toast can display result of javascript expression.']);

$btn = Button::addTo(Ui::layout(), ['label' => 'Show Toast', 'type' => 'outline', 'color' => 'info']);
Jquery::addEventTo($btn, 'click')->execute(JsToast::notifyWithJs(Jquery::withThis()->text(), Jquery::withView($info)->text()));

$form = Form::addTo(Ui::layout());

$form->addControl((new Form\Control\Line(['caption' => 'Title', 'controlName' => 'title']))->setValue('My Toast'));

$types = [
    'default' => 'Default',
    'info' => 'Info',
    'success' => 'Success',
    'warning' => 'Warning',
    'error' => 'Error',
];
/** @var Form\Control\Select $select */
$select = $form->addControl(new Form\Control\Select(['caption' => 'Type', 'controlName' => 'type', 'allowNull' => false]));
$select->setItems($types);
$select->setValue('default');

$form->addControl(new Form\Control\Textarea(['controlName' => 'message', 'caption' => 'Message']))->setValue(Utils::getLoremIpsum(6));
$form->addControl((new Form\Control\Number(['controlName' => 'timeout', 'caption' => 'Duration in ms']))->setValue(3000));
$form->getSubmitButton()->setLabel('Show Toast');

$positions = [
    'top-right' => 'Top Right',
    'top-center' => 'Top Center',
    'top-left' => 'Top-Left',
    'bottom-right' => 'Bottom Right',
    'bottom-center' => 'Bottom Center',
    'bottom-left' => 'Bottom Left',
];
/** @var Form\Control\Select $pos */
$pos = $form->addControl(new Form\Control\Select(['caption' => 'Position', 'controlName' => 'position', 'allowNull' => false]));
$pos->setItems($positions);
$pos->setValue('top-right');

$form->onSubmit(function (Form $f) {
    $values = $f->getControlValues();

    return JsToast::notify($values['title'], $values['message'], $values);
});
