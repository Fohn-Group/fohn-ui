<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;
use Fohn\Ui\View\Tag;

require_once __DIR__ . '/../init-ui.php';

$img = '../images/fohn-logo.png';

$labelBar = Ui::layout()->addView(new View())->appendTailwinds(['flex', 'inline-block', 'mx-2']);
Tag::addTo($labelBar)->setTextContent('Hot!');
Tag::addTo($labelBar, ['iconName' => 'bi-envelope', 'color' => 'info'])->setTextContent('23');
Tag::addTo($labelBar, ['iconName' => 'bi-trash2', 'color' => 'error'])->setTextContent('Item');
Tag::addTo($labelBar, ['imageSrc' => $img])->setTextContent('Logo');
Tag::addTo($labelBar, ['iconName' => 'bi-cup-straw', 'placement' => 'left'])->setTextContent('Beer');
Tag::addTo($labelBar, ['imageSrc' => $img, 'shape' => 'rounded'])->setTextContent('Logo');
Tag::addTo($labelBar, ['iconName' => 'bi-bullseye', 'color' => 'error', 'placement' => 'left', 'shape' => 'rounded'])
    ->setTextContent('Here');
Tag::addTo($labelBar, ['iconName' => 'bi-bullseye', 'color' => 'error', 'placement' => 'left', 'type' => 'outline'])
    ->setTextContent('Here');
