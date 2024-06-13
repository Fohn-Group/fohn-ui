<?php

declare(strict_types=1);

use Fohn\Ui\Component\Table\Filter\Date;
use Fohn\Ui\Component\Table\Filter\DateTime;
use Fohn\Ui\Component\Table\Filter\Number;
use Fohn\Ui\Component\Table\Filter\Text;
use Fohn\Ui\Component\Table\Filter\Time;
use Fohn\Ui\Service\Ui;

require_once __DIR__ . '/../init-ui.php';

// \Fohn\Ui\Component\Utils::requireFLatPickrLocale(\Fohn\Ui\Service\Ui::page(), 'fr', );

$filter = \Fohn\Ui\Component\Table\Filter::addTo(Ui::layout());

$filter->addFilter(new Number('id'));
$filter->addFilter(new Text('name'));
$filter->addFilter(new Date('date', Ui::getDisplayFormat('date')));
$filter->addFilter(new Time('time'));
$filter->addFilter(new DateTime('datetime', Ui::getDisplayFormat('datetime')));

Ui::viewDump($filter, 'filter');
