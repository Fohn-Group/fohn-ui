<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\Service\Ui;
use Fohn\Ui\View\Heading\Header;
use Fohn\Ui\View\Heading\SectionHeader;

require_once __DIR__ . '/../init-ui.php';

$imgSrc = '../images/fohn-logo.png';

Header::addTo(Ui::layout(), ['title' => 'H1 Header', 'size' => 1]);

$header = SectionHeader::addTo(Ui::layout(), ['title' => 'Section Header', 'iconName' => 'bi-house-fill'])
    ->setHeaderSize(2);
$header->addSubTitle('Customize sub title color', 'text-red-400');

/*
$subtitles = [
    'Header and SectionHeader.',
];
DemoApp::addPageHeaderTo(Ui::layout(), 'Header', $subtitles);



// --------------------------
$section = DemoApp::addInfoSection(Ui::layout(), 'Size:');


Header::addTo($section, ['title' => 'H1 Header', 'size' => 1]);
Header::addTo($section, ['title' => 'H2 Header', 'size' => 2]);
Header::addTo($section, ['title' => 'H3 Header', 'size' => 3]);
Header::addTo($section, ['title' => 'H4 Header', 'size' => 4]);
Header::addTo($section, ['title' => 'H5 Header', 'size' => 5]);

// --------------------------
$section = DemoApp::addInfoSection(Ui::layout(), 'Responsive header size using Tailwinds screen modifier:');


Header::addTo($section, ['title' => 'Responsive Header', 'size' => 6])
    ->setSize(4, 'md')
    ->setSize(3, 'lg')
    ->setSize(2, 'xl')
    ->setSize(1, '2xl');

// --------------------------
$section = DemoApp::addInfoSection(Ui::layout(), 'Section header:');

SectionHeader::addTo($section, ['title' => 'Section Header', 'subTitle' => 'Section header may have sub title.'])
    ->setHeaderSize(2)->getHeaderView()->appendTailwind('text-blue-500');

SectionHeader::addTo($section, ['title' => 'Section Header', 'subTitle' => 'Section header may have sub title.', 'imageSrc' => $imgSrc])
    ->setHeaderSize(2)->getHeaderView()->appendTailwind('text-blue-900');

$header = SectionHeader::addTo($section, ['title' => 'Section Header', 'iconName' => 'bi-house-fill'])
    ->setHeaderSize(2);
$header->addSubTitle('Customize sub title color', 'text-red-400');
*/
