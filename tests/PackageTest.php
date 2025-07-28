<?php

declare(strict_types=1);

namespace Fohn\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Fohn\Ui\Page\Package;

class PackageTest extends TestCase
{
    public function testAddScript(): void
    {
        $script = Package::addScript('my-url');
        $this->assertSame('<script type="application/javascript" src="my-url"></script>', $script->getHtmlTag());

        $script = Package::addScript('my-url', true);
        $this->assertSame('<script type="application/javascript" src="my-url" defer></script>', $script->getHtmlTag());

        $script = Package::addScript('my-url', true, true);
        $this->assertSame('<script type="application/javascript" src="my-url" defer async></script>', $script->getHtmlTag());
    }

    public function testAddLink()
    {
        $link = Package::addStylesheet('my-url');
        $this->assertSame('<link rel="stylesheet" type="text/css" href="my-url"/>', $link->getHtmlTag());

        $link = Package::addStylesheet('my-url', true);
        $this->assertSame('<link rel="stylesheet" type="text/css" href="my-url" defer/>', $link->getHtmlTag());
    }

    public function testPackage()
    {
        $attributes = [
            'rel' => 'apple-touch-icon',
            'sizes' => '114x114',
            'href' => 'apple-icon-114.png',
            'type' => 'image/png',
        ];

        $package = new Package('includeCss', 'link/', $attributes, null);
        $this->assertSame('includeCss', $package->getPageRegion());
        $this->assertSame('<link rel="apple-touch-icon" sizes="114x114" href="apple-icon-114.png" type="image/png"/>', $package->getHtmlTag());

        $attributes = [
            'rel' => 'preload',
            'href' => 'myFont.woff2',
            'as' => 'font',
            'type' => 'font/woff2',
        ];

        $package = new Package('includeCss', 'link/', $attributes, null);
        $package->mergeAttributes(['crossorigin' => 'anonymous']);
        $this->assertSame('<link rel="preload" href="myFont.woff2" as="font" type="font/woff2" crossorigin="anonymous"/>', $package->getHtmlTag());
    }
}
