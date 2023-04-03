<?php

declare(strict_types=1);

namespace Fohn\Ui\View;

use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Theme\Base;
use Fohn\Ui\View;

/**
 * Class implements Messages (a visual box).
 */
class Message extends View
{
    public string $defaultTemplate = 'view/message.html';

    /** @var string */
    public $type = 'outline';

    /** @var string */
    public $color = 'warning';

    /** @var string */
    public $title = '';

    /** @var View */
    private $msgContainer;

    /** @var View */
    protected $titleView;

    protected function initRenderTree(): void
    {
        parent::initRenderTree();

        $this->titleView = View::addTo($this);
        $this->msgContainer = View::addTo($this);

        if ($this->title) {
            $this->setTitle($this->title);
        }
    }

    public function setTitle(string $title): self
    {
        $this->titleView->setText($title);

        return $this;
    }

    public function getTitleView(): View
    {
        return $this->titleView;
    }

    public function getMsgContainer(): ?View
    {
        return $this->msgContainer;
    }

    public function addText(string $text): self
    {
        $this->msgContainer->addView(View::factory(['htmlTag' => 'p', 'text' => $text]));

        return $this;
    }

    public function addIcon(string $icon): self
    {
        Icon::addTo($this, ['iconName' => $icon], 'leftContent');

        return $this;
    }

    protected function beforeHtmlRender(): void
    {
        Ui::theme()::styleAs(Base::MESSAGE, [$this]);
        parent::beforeHtmlRender();
    }
}
