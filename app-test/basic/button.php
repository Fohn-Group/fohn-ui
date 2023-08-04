<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;
use Fohn\Ui\View\Button;
use Fohn\Ui\View\Icon;

require_once __DIR__ . '/../init-ui.php';

$bar = View::addTo(Ui::layout(), ['defaultTailwind' => ['inline-block, my-4']]);
Button::addTo($bar, ['label' => 'Link', 'type' => 'link']);

$type = 'contained';
$bar = View::addTo(Ui::layout(), ['defaultTailwind' => ['inline-block, my-4']]);
Button::addTo($bar, ['label' => 'Primary', 'type' => $type]);
Button::addTo($bar, ['label' => 'Secondary', 'color' => 'secondary', 'type' => $type]);
Button::addTo($bar, ['label' => 'Info', 'color' => 'info', 'type' => $type]);
Button::addTo($bar, ['label' => 'Success', 'color' => 'success'])->setType($type);
Button::addTo($bar, ['label' => 'Warning', 'color' => 'warning', 'type' => $type]);
Button::addTo($bar, ['label' => 'Error', 'color' => 'error', 'type' => $type]);
Button::addTo($bar, ['label' => 'Neutral', 'color' => 'neutral', 'type' => $type]);

$type = 'outline';
$bar = View::addTo(Ui::layout(), ['defaultTailwind' => ['inline-block, my-4']]);
Button::addTo($bar, ['label' => 'Primary', 'type' => $type]);
Button::addTo($bar, ['label' => 'Secondary', 'color' => 'secondary', 'type' => $type]);
Button::addTo($bar, ['label' => 'Info', 'color' => 'info', 'type' => $type]);
Button::addTo($bar, ['label' => 'Success', 'color' => 'success'])->setType($type);
Button::addTo($bar, ['label' => 'Warning', 'color' => 'warning', 'type' => $type]);
Button::addTo($bar, ['label' => 'Error', 'color' => 'error', 'type' => $type]);
Button::addTo($bar, ['label' => 'Neutral', 'color' => 'neutral', 'type' => $type]);

$type = 'text';
$bar = View::addTo(Ui::layout(), ['defaultTailwind' => ['inline-block, my-4']]);
Button::addTo($bar, ['label' => 'Primary', 'type' => $type]);
Button::addTo($bar, ['label' => 'Secondary', 'color' => 'secondary', 'type' => $type]);
Button::addTo($bar, ['label' => 'Info', 'color' => 'info', 'type' => $type]);
Button::addTo($bar, ['label' => 'Success', 'color' => 'success'])->setType($type);
Button::addTo($bar, ['label' => 'Warning', 'color' => 'warning', 'type' => $type]);
Button::addTo($bar, ['label' => 'Error', 'color' => 'error', 'type' => $type]);
Button::addTo($bar, ['label' => 'Neutral', 'color' => 'neutral', 'type' => $type]);

$bar = View::addTo(Ui::layout(), ['defaultTailwind' => ['inline-block, my-4']]);
Button::addTo($bar, ['label' => 'Home'])->addIcon(new Icon(['iconName' => 'bi-house-fill']));
Button::addTo($bar, ['label' => 'Play', 'type' => 'outline', 'color' => 'info'])->addIcon(new Icon(['iconName' => 'bi-play-circle']), 'right');
Button::addTo($bar, ['iconName' => 'bi-download']);

$bar = View::addTo(Ui::layout(), ['defaultTailwind' => ['inline-block, my-4']]);
Button::addTo($bar, ['label' => 'Primary'])->disableUsingHtml();

// $bar = View::addTo($grid, ['defaultTailwind' => ['inline-block, my-4']]);
Button::addTo($bar, ['label' => 'Primary'])->appendCssClasses('loading');

$bar = View::addTo(Ui::layout(), ['defaultTailwind' => ['inline-block, my-4']]);
Button::addTo($bar, ['label' => 'Tiny', 'color' => 'neutral'])->setSize('tiny');
Button::addTo($bar, ['label' => 'Small', 'color' => 'neutral'])->setSize('small');
Button::addTo($bar, ['label' => 'Normal', 'color' => 'neutral']);
Button::addTo($bar, ['label' => 'Large', 'color' => 'neutral'])->setSize('large');

$bar = View::addTo(Ui::layout(), ['defaultTailwind' => ['inline-block, my-4']]);
Button::addTo($bar, ['iconName' => 'bi-house-fill'])->setShape('square');
Button::addTo($bar, ['iconName' => 'bi-house-fill', 'color' => 'neutral'])->setShape('circle');
Button::addTo($bar, ['label' => 'Rounded', 'color' => 'secondary'])->setShape('circle');
