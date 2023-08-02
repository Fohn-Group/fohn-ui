<?php

declare(strict_types=1);

use Fohn\Ui\AppTest\Model\Country;
use Fohn\Ui\Component\Form;
use Fohn\Ui\Component\Tabs\Tab;
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

$tabs = Tabs::addTo(Ui::layout());
$tabs->setTabsMenuTemplate(Ui::templateFromFile(__DIR__ . '/template/tabs-menu.html'));

$homeTab = $tabs->addTab(new Tab(['name' => 'home']));
View::addTo($homeTab)->setTextContent('This is Home content.');

$profileTab = $tabs->addTab(new Tab(['name' => 'profile']));
View::addTo($profileTab)->setTextContent('This is Profile content.');

$userTab = $tabs->addTab(new Tab(['name' => 'preferences']));
View::addTo($userTab)->setTextContent('This is Preferences content.');

$adminTab = $tabs->addTab(new Tab(['name' => 'admin']))->disabled();
View::addTo($adminTab)->setTextContent('This is Admin content.');

View\Divider::addTo(Ui::layout());

$tabs = Tabs::addTo(Ui::layout());
$tabs->setTabsMenuTemplate(Ui::templateFromFile(__DIR__ . '/template/tabs-menu-icon.html'));

$homeTab = $tabs->addTab(new Tab(['name' => 'home']))->addProperty('icon', 'bi-house-fill');
View::addTo($homeTab)->setTextContent('This is Home content.');

$profileTab = $tabs->addTab(new Tab(['name' => 'profile']))->addProperty('icon', 'bi-person-fill');
View::addTo($profileTab)->setTextContent('This is Profile content.');

$userTab = $tabs->addTab(new Tab(['name' => 'preferences']))->addProperty('icon', 'bi-gear-fill');
View::addTo($userTab)->setTextContent('This is Preferences content.');

$adminTab = $tabs->addTab(new Tab(['name' => 'admin']))->disabled()->addProperty('icon', 'bi-person-fill-lock');
View::addTo($adminTab)->setTextContent('This is Admin content.');