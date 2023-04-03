<?php

declare(strict_types=1);

namespace Fohn\Ui\View;

use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Theme\Base;
use Fohn\Ui\View;

class Tag extends View
{
    public string $defaultTemplate = 'view/tag.html';

    public string $iconName = '';
    public string $imageSrc = '';
    public string $color = 'neutral';

    /** normal or rounded shape. */
    public string $shape = 'normal';

    /** x-small, small, normal or big. */
    public string $textSize = 'small';
    public string $width = 'fit';

    /** contained or outline */
    public string $type = 'contained';

    /** where to place image or icon. */
    public string $placement = 'right';

    protected Icon $icon;

    protected Image $image;

    public function getIcon(): Icon
    {
        return $this->icon;
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    protected function beforeHtmlRender(): void
    {
        $region = $this->placement === 'right' ? 'beforeContent' : 'afterContent';
        if ($this->iconName) {
            $this->icon = Icon::addTo($this, ['iconName' => $this->iconName], $region);
        }

        if ($this->imageSrc) {
            $this->image = Image::addTo($this, ['src' => $this->imageSrc], $region);
            $this->image
                ->appendTailwinds(
                    [
                        'w-4',
                        'align-center',
                        'inline-block',
                    ]
                );
        }

        Ui::theme()::styleAs(Base::TAG, [$this]);

        parent::beforeHtmlRender();
    }
}
