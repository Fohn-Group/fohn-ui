<?php

declare(strict_types = 1);

use Fohn\Ui\AppTest\Model\Country;
use Fohn\Ui\Component\Form;
use Fohn\Ui\Component\Tab\Tab;
use Fohn\Ui\Component\Tabs;
use Fohn\Ui\Js\Jquery;
use Fohn\Ui\Js\JsReload;
use Fohn\Ui\Js\JsToast;
use Fohn\Ui\Service\Atk\FormModelController;
use Fohn\Ui\Service\Data;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;
use Fohn\Ui\View\Button;

require_once __DIR__ . '/../init-ui.php';

$modelCtrl = new FormModelController(new Country(Data::db()));
$id = (string) $modelCtrl->getModel()->tryLoadAny()->get('id');

$tabs = Tabs::addTo(Ui::layout());

$homeTab = $tabs->addTab(new Tab(['name' => 'home']));

View::addTo($homeTab)->setTextContent('This is home tab content.');

$profileTab = $tabs->addTab(new Tab(['name' => 'profile']));

$form = Form::addTo($profileTab);
$form->addControls($modelCtrl->factoryFormControls($id));
$form->onSubmit(function (Form $f) use ($modelCtrl, $id) {
    if ($errors = $modelCtrl->saveModelUsingForm($id, $f->getControls())) {
        $f->addValidationErrors($errors);
    }

    return JsToast::success('Saved!');
});

$userTab = $tabs->addTab(new Tab(['name' => 'user']));
View::addTo($userTab)->setTextContent('This is user tab content.');
$b = Button::addTo($userTab, ['label' => 'Reload ' . ($_GET['test'] ?? 0)]);
Jquery::addEventTo($b, 'click')->execute(JsReload::view($b, ['test ' => random_int(0, 100)]));

$tabs->activateTabName('profile');

Ui::viewDump($tabs, 'tab');
