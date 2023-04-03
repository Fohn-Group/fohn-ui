<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\Core\Utils;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\View\Message;

require_once __DIR__ . '/../init-ui.php';

$msg = Message::addTo(Ui::layout(), ['title' => 'Outline Info', 'color' => 'info']);
$msg->addText(Utils::getLoremIpsum(random_int(1, 10)));

$msg = Message::addTo(Ui::layout(), ['color' => 'success', 'title' => 'Outline Success']);
$msg->addText(Utils::getLoremIpsum(random_int(1, 10)));

$msg = Message::addTo(Ui::layout(), ['color' => 'error', 'title' => 'Outline Error']);
$msg->addText(Utils::getLoremIpsum(random_int(1, 10)));

$msg = Message::addTo(Ui::layout(), ['type' => 'contained', 'color' => 'warning', 'title' => 'Contained Warning']);
$msg->addText(Utils::getLoremIpsum(random_int(1, 10)));

$msg = Message::addTo(Ui::layout(), ['type' => 'contained', 'color' => 'success', 'title' => 'Contained Success']);
$msg->addText(Utils::getLoremIpsum(random_int(1, 10)));
$msg->addText(Utils::getLoremIpsum(random_int(1, 10)));
$msg->addText(Utils::getLoremIpsum(random_int(1, 10)));

$msg = Message::addTo(Ui::layout(), ['type' => 'contained', 'color' => 'info', 'title' => 'Contained Info with icon']);
$msg->addText(Utils::getLoremIpsum(random_int(1, 10)))->addIcon('bi-house-fill fa-3x');
