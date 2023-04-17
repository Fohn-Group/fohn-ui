<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;
use Fohn\Ui\View\Tag;

require_once __DIR__ . '/../init-ui.php';

$img = '../images/fohn-logo.png';

$labelBar = Ui::layout()->addView(new View())->appendTailwinds(['flex', 'inline-block', 'mx-2']);
Tag::addTo($labelBar, ['htmlContent' => 'Hot!']);
Tag::addTo($labelBar, ['htmlContent' => '23', 'iconName' => 'bi-envelope', 'color' => 'info']);
Tag::addTo($labelBar, ['htmlContent' => 'Item', 'iconName' => 'bi-trash2', 'color' => 'error']);
Tag::addTo($labelBar, ['htmlContent' => 'Logo', 'imageSrc' => $img]);
Tag::addTo($labelBar, ['htmlContent' => 'Beer', 'iconName' => 'bi-cup-straw', 'placement' => 'left']);
Tag::addTo($labelBar, ['htmlContent' => 'Logo', 'imageSrc' => $img, 'shape' => 'rounded']);
Tag::addTo($labelBar, ['htmlContent' => 'Here', 'iconName' => 'bi-bullseye', 'color' => 'error', 'placement' => 'left', 'shape' => 'rounded']);
Tag::addTo($labelBar, ['htmlContent' => 'Here', 'iconName' => 'bi-bullseye', 'color' => 'error', 'placement' => 'left', 'type' => 'outline']);
