<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Tw;
use Fohn\Ui\View;
use Fohn\Ui\View\GridLayout;
use Fohn\Ui\View\Heading\Header;

require_once __DIR__ . '/../init-ui.php';

$viewStyle =
    [
        'text-white',
        'flex',
        'items-center',
        'justify-center',
        'font-extrabold',
    ];

function gridDemo(GridLayout $grid, int $number, array $style, bool $useColPan = false): void
{
    $style[] = $useColPan ? 'bg-blue-300' : 'bg-blue-600';
    for ($x = 1; $x < $number + 1; ++$x) {
        $v = View::addTo($grid)->setHtmlContent((string) $x)->appendTailwinds($style)->appendTailwind(Tw::height('12'));
        if ($useColPan && $x === 4 || $useColPan && $x === 7) {
            $v->appendTailwinds([Tw::gridCol('span', '2'), 'bg-blue-600']);
        }
    }
}

function gridSpanDemo(GridLayout $grid, array $style): void
{
    $grid->appendTailwind('h-64');
    $style[] = Tw::bgColor('info');
    View::addTo($grid)->setHtmlContent('1')->appendTailwinds($style)->appendTailwinds([Tw::gridRow('span', '3')]);
    View::addTo($grid)->setHtmlContent('2')->appendTailwinds($style)->appendTailwinds([Tw::gridCol('span', '2')]);
    View::addTo($grid)->setHtmlContent('3')->appendTailwinds($style)->appendTailwinds([Tw::gridRow('span', '2'), Tw::gridCol('span', '2')]);
}

function gridStartDemo(GridLayout $grid, array $style): void
{
    $grid->appendTailwind('h-64');
    $style[] = 'bg-blue-600';
    View::addTo($grid)->setHtmlContent('1')->appendTailwinds($style)->appendTailwinds([Tw::gridCol('start', '2'), Tw::gridCol('span', '4')]);
    View::addTo($grid)->setHtmlContent('2')->appendTailwinds($style)->appendTailwinds([Tw::gridCol('start', '1'), Tw::gridCol('end', '3')]);
    View::addTo($grid)->setHtmlContent('3')->appendTailwinds($style)->appendTailwinds([Tw::gridCol('end', '7'), Tw::gridCol('span', '2')]);
    View::addTo($grid)->setHtmlContent('4')->appendTailwinds($style)->appendTailwinds([Tw::gridCol('start', '1'), Tw::gridCol('end', '7')]);
}

Header::addTo(Ui::layout(), ['title' => 'Grid using row direction', 'size' => 5]);

$gridLayout = GridLayout::addTo(Ui::layout(), ['columns' => 3, 'rows' => 3]);
gridDemo($gridLayout, 9, $viewStyle);

Header::addTo(Ui::layout(), ['title' => 'Grid using col direction', 'size' => 5]);
$gridLayout = GridLayout::addTo(Ui::layout(), ['columns' => 3, 'rows' => 3, 'direction' => 'col']);
gridDemo($gridLayout, 9, $viewStyle);

Header::addTo(Ui::layout(), ['title' => 'Grid col span utility', 'size' => 5]);
$gridLayout = GridLayout::addTo(Ui::layout(), ['columns' => 3, 'rows' => 3]);
gridDemo($gridLayout, 7, $viewStyle, true);

Header::addTo(Ui::layout(), ['title' => 'Grid row/col span utitlity', 'size' => 5]);
$gridLayout = GridLayout::addTo(Ui::layout(), ['columns' => 3, 'rows' => 3, 'direction' => 'col']);
gridSpanDemo($gridLayout, $viewStyle);

Header::addTo(Ui::layout(), ['title' => 'Grid col start/end utility', 'size' => 5]);
$gridLayout = GridLayout::addTo(Ui::layout(), ['columns' => 6, 'rows' => 3, 'direction' => 'col']);
gridStartDemo($gridLayout, $viewStyle);
