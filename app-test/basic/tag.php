<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;
use Fohn\Ui\View\Tag;

require_once __DIR__ . '/../init-ui.php';

$img = '../images/fohn-logo.png';

$labelBar = Ui::layout()->addView(new View())->appendTailwinds(['flex', 'inline-block', 'mx-2']);
Tag::addTo($labelBar, ['text' => 'Hot!']);
Tag::addTo($labelBar, ['text' => '23', 'iconName' => 'bi-envelope', 'color' => 'info']);
Tag::addTo($labelBar, ['text' => 'Item', 'iconName' => 'bi-trash2', 'color' => 'error']);
Tag::addTo($labelBar, ['text' => 'Logo', 'imageSrc' => $img]);
Tag::addTo($labelBar, ['text' => 'Beer', 'iconName' => 'bi-cup-straw', 'placement' => 'left']);
Tag::addTo($labelBar, ['text' => 'Logo', 'imageSrc' => $img, 'shape' => 'rounded']);
Tag::addTo($labelBar, ['text' => 'Here', 'iconName' => 'bi-bullseye', 'color' => 'error', 'placement' => 'left', 'shape' => 'rounded']);
Tag::addTo($labelBar, ['text' => 'Here', 'iconName' => 'bi-bullseye', 'color' => 'error', 'placement' => 'left', 'type' => 'outline']);
