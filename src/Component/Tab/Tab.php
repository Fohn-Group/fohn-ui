<?php

declare(strict_types=1);
/**
 * Tab Component.
 */

namespace Fohn\Ui\Component\Tab;

use Fohn\Ui\Component\VueInterface;
use Fohn\Ui\Component\VueTrait;
use Fohn\Ui\Js\Js;
use Fohn\Ui\View;

class Tab extends View implements VueInterface
{
    use VueTrait;

    protected const COMP_NAME = 'fohn-tab';

    public string $defaultTemplate = 'vue-component/tabs/tab.html';

    public string $tabStoreId;

    protected string $name = '';
    protected string $caption = '';

    protected function initRenderTree(): void
    {
        parent::initRenderTree();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setCaption(string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    protected function beforeHtmlRender(): void
    {
        $this->getTemplate()->trySetJs('tabName', Js::string($this->getName()));
        $this->getTemplate()->trySetJs('tabStoreId', Js::string($this->tabStoreId));

        parent::beforeHtmlRender();
    }
}
