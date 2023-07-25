<?php

declare(strict_types = 1);
/**
 * Tabs component.
 */

namespace Fohn\Ui\Component;

use Fohn\Ui\Component\Tab\Tab;
use Fohn\Ui\Core\Exception;
use Fohn\Ui\Js\Type\Type;

class Tabs extends \Fohn\Ui\View
{
    use VueTrait;

    protected const COMP_NAME = 'fohn-tabs';
    protected const TAB_REGION_NAME = 'tabs';

    private const PINIA_PREFIX = '__tabs_';


    public string $defaultTemplate = 'vue-component/tabs.html';

    /** @var array<Tab> */
    protected array $tabs = [];

    protected function initRenderTree(): void
    {
        parent::initRenderTree();
    }

    public function addTab(Tab $tab): Tab
    {
        $this->registerTab($tab);
        $this->addView($tab, self::TAB_REGION_NAME);

        return $tab;
    }

    protected function registerTab(Tab $tab): void
    {
        $this->assertTabHasName($tab->getName());
        $this->assertTabIsUnique($tab->getName());

        if (!$tab->getCaption()) {
            $tab->setCaption(ucfirst($tab->getName()));
        }

        $tab->tabStoreId = $this->getPiniaStoreId(self::PINIA_PREFIX);
        $this->tabs[$tab->getName()] = $tab;
    }

    private function assertTabHasName(string $tabName): void
    {
        if (!$tabName) {
            throw new Exception('Tab must have a name.');
        }
    }

    private function assertTabIsUnique(string $tabName): void
    {
        if (array_key_exists($tabName, $this->tabs)) {
            throw (new Exception('This tab name is already added.'))
                ->addMoreInfo('Tab name:', $tabName);
        }
    }

    protected function setTemplateProps(): void
    {
        $props['storeId'] = $this->getPiniaStoreId(self::PINIA_PREFIX);

        $tabList = [];
        foreach ($this->tabs as $tab) {
            $tabList[] = ['name' => $tab->getName(), 'caption' => $tab->getCaption()];
        }
        $props['tabList'] = $tabList;

        foreach ($props as $key => $value) {
            $this->getTemplate()->setJs($key, Type::factory($value));
        }
    }

    protected function beforeHtmlRender(): void
    {
        // todo move into props
        $this->setTemplateProps();

        $this->createVueApp(self::COMP_NAME, [], $this->getDefaultSelector());
        parent::beforeHtmlRender();
    }
}
