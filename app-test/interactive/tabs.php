<?php

declare(strict_types = 1);

use Fohn\Ui\Component\Tab\Tab;
use Fohn\Ui\Component\Tabs;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;

require_once __DIR__ . '/../init-ui.php';

$tabs = Tabs::addTo(Ui::layout());

$tab = $tabs->addTab(new Tab(['name' => 'home']));

$tab->addView((new View())->setTextContent('This is home tab'));

$tab = $tabs->addTab(new Tab(['name' => 'profile']));


$tab->addView((new View())->setTextContent('This is profile tab'));

Ui::viewDump($tabs, 'tab');
