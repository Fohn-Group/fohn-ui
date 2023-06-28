<?php
/**
 * Button.
 */
declare(strict_types=1);

namespace Fohn\Ui\View;

use Fohn\Ui\Js\Jquery;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsRenderInterface;
use Fohn\Ui\Service\Theme\Base;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Tw;
use Fohn\Ui\View;

class Button extends View
{
    public const ICON_LEFT_REGION = 'leftIcon';
    public const ICON_RIGHT_REGION = 'rightIcon';

    /** @var string[] */
    public array $defaultTailwind = [
        'inline-block',
        'font-medium',
        'text-center',
        'mx-2',
    ];

    public string $defaultTemplate = 'view/button.html';
    public string $htmlTag = 'button';
    protected string $label = '';

    /** Possible value are: auto, normal, wide, block, square, circle. */
    protected string $shape = 'auto';

    /** values: tiny, small, normal, large */
    protected string $size = 'normal';

    protected ?string $iconName = null;

    protected string $type = 'contained';

    protected string $color = 'primary';

    private ?Icon $icon = null;

    protected function initRenderTree(): void
    {
        parent::initRenderTree();

        if ($this->iconName) {
            $this->addIcon(new Icon(['iconName' => $this->iconName]));
        }
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(): string
    {
        return $this->size;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getShape(): string
    {
        return $this->shape;
    }

    public function setShape(string $shape): self
    {
        $this->shape = $shape;

        return $this;
    }

    public function disableUsingHtml(): self
    {
        $this->appendHtmlAttribute('disabled', null);

        return $this;
    }

    public function enableUsingJavascript(): JsRenderInterface
    {
        return Jquery::withView($this)->attr('disabled', false);
    }

    public function disableUsingJavascript(): JsRenderInterface
    {
        return Jquery::withView($this)->attr('disabled', true);
    }

    public function jsLinkTo(string $url): self
    {
        $location = Js::from('document.location = {{url}}', ['url' => $url])->jsRender();
        $this->appendHtmlAttribute('onclick', str_replace(' ', '', $location));

        return $this;
    }

    public function getIcon(): ?Icon
    {
        return $this->icon;
    }

    // Add icon at position: left or right
    public function addIcon(Icon $icon, string $position = 'left'): Icon
    {
        $this->icon = Icon::addSelfTo($this, $icon, $position === 'left' ? self::ICON_LEFT_REGION : self::ICON_RIGHT_REGION);

        if ($this->label) {
            $this->icon->appendTailwinds([Tw::paddingAt($position === 'left' ? 'right' : 'left', '2')]);
        }

        return $this->icon;
    }

    protected function beforeHtmlRender(): void
    {
        Ui::theme()::styleAs(Base::BUTTON, [$this]);
        $this->getTemplate()->set('label', $this->label);

        parent::beforeHtmlRender();
    }
}
