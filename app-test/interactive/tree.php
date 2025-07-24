<?php

declare(strict_types=1);


use Fohn\Ui\Component\Tree;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Js\JsToast;
use Fohn\Ui\Js\JsRenderInterface;

require_once __DIR__ . '/../init-ui.php';

$nodes = [
    [
        'key' => 'document',
        'label' => 'Documents',
        'data' => 'Documents Folder',
        'icon' => 'bi bi-folder',
        'children' => [
            [
                'key' => 'work',
                'label' => 'Works',
                'data' => 'Works Folder',
                'icon' => 'bi bi-gear',
                'children' => [
                    ['key' => 'expense', 'label' => 'Expenses.doc', 'data' => 'Expenses.doc', 'icon' => 'bi bi-filetype-doc'],
                    ['key' => 'resume', 'label' => 'Resume.doc', 'data' => 'Resume.doc', 'icon' => 'bi bi-filetype-doc'],
                ],
            ],
            [
                'key' => 'home',
                'label' => 'Home',
                'data' => 'Home Folder',
                'icon' => 'bi bi-house',
                'children' => [
                    ['key' => 'invoice', 'label' => 'Invoices.txt', 'data' => 'Invoice.txt', 'icon' => 'bi bi-filetype-txt'],
                ],
            ],
        ],
    ],
];

$tree = Tree::addTo(Ui::layout(), ['ptProps' => ['expandedKeys' => ['document' => true]]]);
$tree->setNodes($nodes);
$tree->setSelectionMode('multiple');
$tree->setFilter(['label'], 'lenient', 'Search document');

$tree->onTreePost(static function ($keys, $rawValue): JsRenderInterface {
    // Process callback and return notification to user.
    return JsToast::notify('Post: ', 'value: ' . implode(', ', $keys));
});
