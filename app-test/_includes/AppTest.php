<?php

declare(strict_types=1);

/**
 * Utility class for the Test application.
 */

namespace Fohn\Ui\AppTest;

use Fohn\Ui\Component\Navigation\Group;
use Fohn\Ui\Component\Navigation\Item;
use Fohn\Ui\Page;
use Fohn\Ui\PageLayout\SideNavigation;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Tw;
use Fohn\Ui\View;
use Fohn\Ui\View\Button;

class AppTest
{
    public static function createPage(string $environment = 'production'): Page
    {
        $path = Ui::serverRequest()->getUri()->getPath();
        $lastWord = substr($path, strrpos($path, '/') + 1);

        /** @var Page $page */
        $page = Page::factory([
            'title' => 'Fohn-ui Test: ' . $lastWord,
        ]);

        if ($environment === 'dev') {
            $page->includeJsPackage('fohn-js', '/public/fohn-ui.js');
            $page->includeCssPackage('fohn-css', '/public/fohn-ui.css');
        }

        // Add Admin layout to this page.
        $page->addLayout(SideNavigation::factory(['topBarTitle' => 'Fohn-Ui Test App']));

        /** @var SideNavigation $layout */
        $layout = $page->getLayout();
        // Add footer to this page.
        $layout->addView(View::factory(['htmlTag' => 'div']), 'footer')
            ->setTextContent('Made with Fohn - Ui');

        foreach (self::getNavigationGroup() as $group) {
            $layout->addNavigationGroup($group);

            foreach ($group->items as $item) {
                if ($path === $item->url) {
                    $page->title = 'Fohn-ui Test: ' . $item->name;

                    break;
                }
            }
        }

        return $page;
    }

    private static function getNavigationGroup(string $baseUrl = '/app-test/'): array
    {
        return [
            new Group([
                'name' => 'Basics',
                'icon' => 'bi bi-box',
                'url' => $baseUrl . 'basic/view.php',
                'items' => [
                    new Item(['name' => 'View', 'url' => $baseUrl . 'basic/view.php']),
                    new Item(['name' => 'Button/Link', 'url' => $baseUrl . 'basic/button.php']),
                    new Item(['name' => 'Header', 'url' => $baseUrl . 'basic/header.php']),
                    new Item(['name' => 'Message', 'url' => $baseUrl . 'basic/message.php']),
                    new Item(['name' => 'Tag', 'url' => $baseUrl . 'basic/tag.php']),
                    new Item(['name' => 'Grid', 'url' => $baseUrl . 'basic/grid-layout.php']),
                    new Item(['name' => 'List', 'url' => $baseUrl . 'basic/list.php']),
                    new Item(['name' => 'Breadcrumb', 'url' => $baseUrl . 'basic/breadcrumb.php']),
                ],
            ]),
            new Group([
                'name' => 'Form',
                'icon' => 'bi bi-input-cursor',
                'url' => $baseUrl . 'form/form.php',
                'items' => [
                    new Item(['name' => 'Basic Layout', 'url' => $baseUrl . 'form/form.php']),
                    new Item(['name' => 'Custom Layout', 'url' => $baseUrl . 'form/form-left.php']),
                    new Item(['name' => 'Input Template', 'url' => $baseUrl . 'form/form-custom-input.php']),
                    new Item(['name' => 'Controls', 'url' => $baseUrl . 'form/form-control.php']),
                    new Item(['name' => 'Model Controller', 'url' => $baseUrl . 'form/form-model-controller.php']),
                ],
            ]),
            new Group([
                'name' => 'Collection',
                'icon' => 'bi bi-collection',
                'url' => $baseUrl . 'collection/table.php',
                'items' => [
                    new Item(['name' => 'Table', 'url' => $baseUrl . 'collection/table.php']),
                    new Item(['name' => 'Table w. Atk Model', 'url' => $baseUrl . 'collection/table-as-crud.php']),
                ],
            ]),
            new Group([
                'name' => 'Javascript',
                'icon' => 'bi bi-code-slash',
                'url' => $baseUrl . 'javascript/js.php',
                'items' => [
                    new Item(['name' => 'jQuery Integration', 'url' => $baseUrl . 'javascript/js.php']),
                ],
            ]),
            new Group([
                'name' => 'Interactive',
                'icon' => 'bi bi-chat-left',
                'url' => $baseUrl . 'interactive/virtual.php',
                'items' => [
                    new Item(['name' => 'Modal', 'url' => $baseUrl . 'interactive/modal.php']),
                    new Item(['name' => 'Toast', 'url' => $baseUrl . 'interactive/toast.php']),
                    new Item(['name' => 'Virtual Page', 'url' => $baseUrl . 'interactive/virtual.php']),
                    new Item(['name' => 'Server Side Event', 'url' => $baseUrl . 'interactive/sse.php']),
                    new Item(['name' => 'Console', 'url' => $baseUrl . 'interactive/console.php']),
                ],
            ]),
        ];
    }

    /**
     * Create button suitable to use in a table action column.
     */
    public static function tableBtnFactory(string $iconName, string $color = 'info'): View\Button
    {
        $btn = new Button(['iconName' => $iconName, 'color' => $color, 'shape' => 'circle', 'size' => 'small', 'type' => 'text']);
        $btn->removeTailwind('mx-2');

        return $btn;
    }

    public static function tableCaptionFactory(string $caption): View
    {
        return (new View([
            'defaultTailwind' => [
                'my-2',
                'text-lg',
                Tw::textColor('info'),
            ],
        ]))->setTextContent($caption);
    }
}
