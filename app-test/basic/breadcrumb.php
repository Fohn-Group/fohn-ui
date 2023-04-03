<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Theme\Fohn;
use Fohn\Ui\View\Breadcrumb;

require_once __DIR__ . '/../init-ui.php';

$pages = ['Home', 'Product', 'Electronic', 'Computer'];
$addBreadcrumbItems = function (Breadcrumb $breadcrumb, array $items, string $separator = '/') {
    foreach ($items as $k => $item) {
        $isLast = $k === count($items) - 1;
        if (!$isLast) {
            $breadcrumb->addLink($item, '#', $isLast, $separator);
        } else {
            $breadcrumb->addLast($item);
        }
    }
};

$breadcrumb = Breadcrumb::addTo(Ui::layout());
$addBreadcrumbItems($breadcrumb, $pages);

$breadcrumb = Breadcrumb::addTo(Ui::layout());
$addBreadcrumbItems($breadcrumb, $pages, '>');

$breadcrumb = Breadcrumb::addTo(Ui::layout());
$addBreadcrumbItems($breadcrumb, $pages, '•');
Fohn::colorAs('info', $breadcrumb, 'outline');
$breadcrumb->appendTailwinds(['shadow', 'p-4', 'rounded-md']);
