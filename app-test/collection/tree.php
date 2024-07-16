<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\AppTest\Model\File;
use Fohn\Ui\Component\Tree;
use Fohn\Ui\Js\JsToast;
use Fohn\Ui\Service\Data;
use Fohn\Ui\Service\Ui;

require_once __DIR__ . '/../init-ui.php';

$fileModel = new File(Data::db());

$tree = Tree::addTo(Ui::layout());

$tree->setNodes($fileModel->getFilesHierarchy());
$tree->setSelectionMode($_GET['mode'] ?? 'single')
    ->setHeight('600px')
    ->setFilter(['label', 'type'], 'lenient', 'Search file');

$tree->onNodeSelected(static function ($tree, $key) {
    return JsToast::notify('node selected', $key);
});

Ui::viewDump($tree, 'tree');
