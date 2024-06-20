<?php

declare(strict_types=1);

use Fohn\Ui\Component\Table\Filter;
use Fohn\Ui\Component\Table\Filter\Date;
use Fohn\Ui\Component\Table\Filter\DateTime;
use Fohn\Ui\Component\Table\Filter\Integer;
use Fohn\Ui\Component\Table\Filter\Text;
use Fohn\Ui\Component\Table\Filter\Time;
use Fohn\Ui\Component\Utils;
use Fohn\Ui\Service\Ui;

require_once __DIR__ . '/../init-ui.php';

if ($locale = Ui::service()->getQueryParamValue('locale')) {
    Utils::requireFLatPickrLocale(Ui::page(), $locale);
}

$filter = Filter::addTo(Ui::layout());

$filter->addColumnFilter(new Integer('id'));
$filter->addColumnFilter(new Text('name'));
$filter->addColumnFilter(new Date('date', Ui::getDisplayFormat('date')));
$filter->addColumnFilter(new Time('time'));
$filter->addColumnFilter(new DateTime('datetime', Ui::getDisplayFormat('datetime')));

Ui::viewDump($filter, 'filter');
