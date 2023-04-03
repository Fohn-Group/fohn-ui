<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Faker\Factory;
use Fohn\Ui\HtmlTemplate;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Tw;
use Fohn\Ui\View;
use Fohn\Ui\View\GridLayout;
use Fohn\Ui\View\Lister;

require_once __DIR__ . '/../init-ui.php';

$subtitles = [
    'Lister use an item template to render items as html.',
    'Each item is rendered using this item template.',
    'Finally, the complete rendered items is set in Lister main template.',
];

// Utility function in order to create data.
$factoryPeople = function (int $number): array {
    $faker = Factory::create();
    $items = [];
    for ($i = 0; $i < $number; ++$i) {
        $maleFemale = ($i % 2) === 0 ? 'male' : 'female';
        $items[$i] = [
            'name' => $faker->firstName($maleFemale) . ' ' . $faker->lastName,
            'gender' => $faker->title($maleFemale),
            'imgSrc' => '/app-test/images/avatar.png',
            'jobTitle' => $faker->jobTitle,
        ];
    }

    return $items;
};

$gridLayout = GridLayout::addTo(Ui::layout(), ['columns' => 2, 'gap' => 4])->appendTailwinds(['md:grid-cols-4', 'md:gap-2']);

$segment = View\Segment::addTo($gridLayout);
View\Heading\Header::addTo($segment, ['title' => 'Disc', 'size' => 5]);
// The template, 'lister-item.html', will be repeated for each people and rendered into the 'Items' region of the main Lister template.
View\HtmlList::addTo($segment)->setItems($factoryPeople(6));

$segment = View\Segment::addTo($gridLayout);
View\Heading\Header::addTo($segment, ['title' => 'Decimal', 'size' => 5]);
View\HtmlList::addTo($segment, ['style' => 'decimal'])->setItems($factoryPeople(6));

$segment = View\Segment::addTo($gridLayout);
View\Heading\Header::addTo($segment, ['title' => 'Upper Roman', 'size' => 5]);
View\HtmlList::addTo($segment, ['style' => '[upper-roman]'])->setItems($factoryPeople(6));

$segment = View\Segment::addTo($gridLayout);
View\Heading\Header::addTo($segment, ['title' => 'None', 'size' => 5]);
View\HtmlList::addTo($segment, ['style' => 'non'])->setItems($factoryPeople(6));

// // CUSTOM TEMPLATE

// In this example, each person is rendered using the 'list-person.html' template.
// Each item report is rendered using 'people-report.html' template.
// The results of both html is set within the Lister main template 'list-person.html' within their respective template region.
Lister::addTo(Ui::layout(), ['template' => Ui::templateFromFile(__DIR__ . '/template/list-person.html')])
    ->setRegionItems('peopleCard', $factoryPeople(4), Ui::templateFromFile(__DIR__ . '/template/people-card.html'))
    ->setRegionItems('peopleReport', [['dataInfo' => 'Publish: ' . date('Y-m-d')], ['dataInfo' => 'Source: Fohn-Ui']], Ui::templateFromFile(__DIR__ . '/template/people-report.html'));

// // CALLBACK RENDERING

$list = View\HtmlList::addTo(Ui::layout());
$list->setItems($factoryPeople(10));

$list->onItemRender('Items', function (HtmlTemplate $template, array $tags) {
    $newT = new HtmlTemplate('<li> <span class="{$classAttr}">{$gender}</span> <span>{$name}</span>');
    if ($tags['gender'] === 'Ms.' || $tags['gender'] === 'Mrs.' || $tags['gender'] === 'Mss') {
        $newT->set('classAttr', Tw::textColor('secondary'));
    } else {
        $newT->set('classAttr', Tw::textColor('primary'));
    }

    $newT->trySetMany($tags);

    return $newT->renderToHtml();
});
