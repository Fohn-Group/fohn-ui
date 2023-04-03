<?php

declare(strict_types=1);
/**
 * Admin layout navigation Vue component.
 */

namespace Fohn\Ui\Component;

use Fohn\Ui\Component\Navigation\Group;
use Fohn\Ui\View;

class Navigation extends View implements VueInterface
{
    use VueTrait;

    private const COMP_NAME = 'fohn-navigation';
    protected const PROP_NAVIGATION = 'navigation';
    protected const GROUP_PROPS = 'groups';

    public string $defaultTemplate = 'vue-component/navigation.html';
    public string $width = '52';
    public string $breakPoint = 'lg';
    protected string $title = '';

    /** @var Group[] */
    public array $groups = [];

    /** @var string[] Closing and opening icons for group. */
    protected array $icons = [
        'close' => 'bi bi-caret-down',
        'open' => 'bi bi-caret-up',
    ];

    protected function beforeHtmlRender(): void
    {
        $this->getTemplate()->trySet('width', $this->width);
        $this->getTemplate()->trySet('breakPoint', $this->breakPoint);

        $this->createVueApp(self::COMP_NAME, $this->getRootData(), $this->getDefaultSelector());
        parent::beforeHtmlRender();
    }

    public function addGroup(Group $group): void
    {
        $this->groups[] = $group;
    }

    private function renderGroups(): array
    {
        $groups = [];

        foreach ($this->groups as $group) {
            // @var Group $group
            $groups[] = $group->getGroupRootData();
        }

        return $groups;
    }

    protected function getRootData(): array
    {
        $rootData[self::PROP_NAVIGATION] = [
            'title' => $this->title,
            'icons' => $this->icons,
        ];
        $rootData[self::PROP_NAVIGATION][self::GROUP_PROPS] = $this->renderGroups();

        return $rootData;
    }
}
