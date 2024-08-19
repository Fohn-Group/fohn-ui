<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\AppTest\Model\File;
use Fohn\Ui\Component\Tree;
use Fohn\Ui\Js\JsToast;
use Fohn\Ui\Service\Data;
use Fohn\Ui\Service\Ui;

require_once __DIR__ . '/../init-ui.php';

$mode = $_GET['mode'] ?? 'single';
$allowFolderSelect = ($_GET['folder'] ?? null) === null;

// some file selection in checkbox mode.
$treeValue = [
    159 => [
        'checked' => true,
        'partialChecked' => false,
    ],
    160 => [
        'checked' => true,
        'partialChecked' => false,
    ],
    161 => [
        'checked' => true,
        'partialChecked' => false,
    ],
    162 => [
        'checked' => true,
        'partialChecked' => false,
    ],
    163 => [
        'checked' => true,
        'partialChecked' => false,
    ],
    164 => [
        'checked' => true,
        'partialChecked' => false,
    ],
    165 => [
        'checked' => true,
        'partialChecked' => false,
    ],
    166 => [
        'checked' => false,
        'partialChecked' => true,
    ],
    167 => [
        'checked' => true,
        'partialChecked' => false,
    ],
    168 => [
        'checked' => true,
        'partialChecked' => false,
    ],
];

$fileModel = new File(Data::db());
$tree = Tree::addTo(Ui::layout());
if ($mode === 'checkbox') {
    $tree->setValue($treeValue);
}
$tree->setNodes($fileModel->getFilesHierarchy($allowFolderSelect));
$tree->setSelectionMode($mode)
    ->setHeight('600px')
    ->setFilter(['label', 'type'], 'lenient', 'Search file');

$onChangedMode = $tree->stickyGet('event') === 'changed';
if ($onChangedMode) {
    $tree->onTreeNodeChanged(static function ($action, $key) {
        return JsToast::notify(ucfirst($action), 'Key: ' . $key);
    });
} else {
    $tree->onTreePost(static function ($keys, $rawValue) {
        return JsToast::notify('Post: ', 'value: ' . implode(', ', $keys));
    });
}

Ui::viewDump($tree, 'tree');
