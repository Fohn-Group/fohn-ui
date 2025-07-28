<?php

declare(strict_types=1);

/**
 * External package to be include in page.html.
 */

namespace Fohn\Ui\Page;

use Fohn\Ui\Page;
use Fohn\Ui\Service\Ui;

class Package
{
    public static function addScript(string $url, bool $defer = false, bool $async = false): self
    {
        $attributes = [
            'type' => 'application/javascript',
            'src' => $url,
            'defer' => $defer,
            'async' => $async,
        ];

        return new self(Page::JS_PACKAGE_TAG_REGION, 'script', $attributes, '');
    }

    public static function addLink(string $url, bool $defer = false): self
    {
        $attributes = [
            'rel' => 'stylesheet',
            'type' => 'text/css',
            'href' => $url,
            'defer' => $defer,
        ];

        return new self(Page::CSS_PACKAGE_TAG_REGION, 'link/', $attributes, null);
    }

    public function __construct(
        protected string $pageRegion,
        protected string $tagName,
        protected array $attributes,
        protected ?string $tagValue
    ) {}

    public function mergeAttributes(array $attributes): void
    {
        $this->attributes = array_merge($this->attributes, $attributes);
    }

    public function getPageRegion(): string
    {
        return $this->pageRegion;
    }

    public function getHtmlTag(): string
    {
        return Ui::service()->buildHtmlTag($this->tagName, $this->attributes, $this->tagValue);
    }
}
