<?php

declare(strict_types=1);

namespace Fohn\Ui\View\Heading;

use Fohn\Ui\Tailwind\Tw;
use Fohn\Ui\View;

/**
 * Class implements Html Header h<n>.
 */
class Header extends View
{
    public const FONT_SIZE_MAP = [
        1 => '5xl',
        2 => '4xl',
        3 => '3xl',
        4 => '2xl',
        5 => 'xl',
        6 => 'lg',
    ];

    /** Header size. */
    public int $size = 1;
    public ?string $title = null;
    public bool $hasMargin = true;
    private array $sizeVariant = [];

    protected function initRenderTree(): void
    {
        parent::initRenderTree();

        if ($this->hasMargin) {
            $this->appendTailwinds([
                'my-2',
            ]);
        }

        $this->appendTailwind(Tw::textSize(self::FONT_SIZE_MAP[$this->size]));

        $this->sizeVariant['base'] = self::FONT_SIZE_MAP[$this->size];
    }

    public function setSize(int $size, string $variant = ''): self
    {
        $mapVariant = $variant ?: 'base';

        if ($this->sizeVariant[$mapVariant] ?? null) {
            $this->removeTailwind(Tw::textSize($this->sizeVariant[$mapVariant], $variant));
        }

        $this->appendTailwind(Tw::textSize(self::FONT_SIZE_MAP[$size], $variant));

        $this->sizeVariant[$mapVariant] = self::FONT_SIZE_MAP[$size];

        return $this;
    }

    protected function beforeHtmlRender(): void
    {
        $this->setHtmlTag('h' . $this->size);
        $this->getTemplate()->trySet('Content', $this->title);

        parent::beforeHtmlRender();
    }
}
