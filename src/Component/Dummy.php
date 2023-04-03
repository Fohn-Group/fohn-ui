<?php

declare(strict_types=1);
/**
 * Testing Vue component.
 */

namespace Fohn\Ui\Component;

use Fohn\Ui\View;

class Dummy extends View implements VueInterface
{
    use VueTrait;

    private const COMP_NAME = 'fohn-dummy';
    public string $defaultTemplate = 'vue-component/dummy.html';

    protected function initRenderTree(): void
    {
        parent::initRenderTree();
    }

    public function beforeHtmlRender(): void
    {
        $this->createVueApp(self::COMP_NAME, $this->getRootData(), $this->getDefaultSelector());

        parent::beforeHtmlRender();
    }

    protected function getRootData(): array
    {
        return ['phrase' => 'dummy it is for sure'];
    }
}
