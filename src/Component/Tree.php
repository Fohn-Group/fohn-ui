<?php

declare(strict_types=1);
/**
 * Tree Selector.
 * Extended PrimeVue base Tree.
 */

namespace Fohn\Ui\Component;

use Fohn\Ui\Callback\Ajax;
use Fohn\Ui\Core\HookFn;
use Fohn\Ui\Core\HookTrait;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsRenderInterface;
use Fohn\Ui\Service\Theme\Fohn;
use Fohn\Ui\Service\Theme\TwConstant;
use Fohn\Ui\Tailwind\Tw;
use Fohn\Ui\View;

class Tree extends View implements VueInterface
{
    use HookTrait;
    use VueTrait;

    public const TREE_SELECTED_COLOR = 'selectedColors';
    public const TREE_HOVER_COLOR = 'hoverColors';
    public const TREE_COLLAPSED_ICON = 'collapsedIcon';
    public const TREE_EXPANDED_ICON = 'expandedIcon';

    public const HOOK_NODE_SELECT = self::class . '@selectNode';

    public string $selectedColor = 'info';

    public string $defaultTemplate = 'vue-component/tree.html';

    public array $defaultTailwind = [
        'border',
        'border-gray-300',
        'rounded-md',
    ];

    protected const COMP_NAME = 'fohn-tree';

    protected array $config = [];

    protected array $nodes = [];

    protected array $ptProps = [];
    public array $filterInputDefaultTws = [
        'mt-1',
        'w-1/2',
        'rounded-md',
        'border-gray-300',
        'shadow-sm',
        'focus:border-blue-300',
        'focus:ring-0',
        'focus:ring-blue-200',
        'focus:ring-opacity-50',
    ];

    protected array $treeOptions = [];

    protected ?Ajax $treeRequest = null;

    protected function initRenderTree(): void
    {
        parent::initRenderTree();

        $this->setColorsOptions(implode(' ', Fohn::getColorRecipes($this->selectedColor)), 'hover:bg-' . TwConstant::COLORS['neutral-light']);
        $this->setIconOptions('bi bi-caret-right', 'bi bi-caret-down');
    }

    protected function initAjaxRequest(string $mode): void
    {
        if (!$this->treeRequest) {
            $this->treeRequest = Ajax::addAbstractTo($this);
        }
        $this->treeOptions[$mode] = $this->treeRequest->getUrl();
    }

    public function onNodeSelected(\Closure $fx): void
    {
        $this->initAjaxRequest('selectUrl');
        $this->onHook(self::HOOK_NODE_SELECT, $fx);

        $this->treeRequest->onAjaxPostRequest(function (array $payload): JsRenderInterface {
            $nodeKey = '1'; // isset($payload['__nodeKey']) ? (string) $payload['__nodeKey'] : null;

            return $this->callHook(self::HOOK_NODE_SELECT, HookFn::withJsRenderInterface([$this, $nodeKey]));
        });
    }

    public function setFilter(array $fields, string $mode = 'lenient', string $placeholder = null, ?string $locale = 'en'): self
    {
        $this->ptProps['filter'] = true;
        $this->ptProps['filterBy'] = implode(',', $fields);
        $this->ptProps['filterMode'] = $mode;
        $this->ptProps['filterPlaceholder'] = $placeholder;
        $this->ptProps['filterLocale'] = $locale;
        $this->ptProps['pt']['filterInputClass'] = Tw::from($this->filterInputDefaultTws)->toString();

        return $this;
    }

    public function setSelectionMode(string $mode, bool $useMetaKey = false): self
    {
        $this->ptProps['selectionMode'] = $mode;
        $this->ptProps['metaKeySelection'] = $useMetaKey;

        return $this;
    }

    public function setHeight(string $height): self
    {
        $this->ptProps['scrollHeight'] = $height;

        return $this;
    }

    public function setColorsOptions(string $selectedColors, string $hoverColors): void
    {
        $this->ptProps['pt'][static::TREE_SELECTED_COLOR] = $selectedColors;
        $this->ptProps['pt'][static::TREE_HOVER_COLOR] = $hoverColors;
    }

    public function setIconOptions(string $collapsedIcon, string $expandedIcon): void
    {
        $this->treeOptions[static::TREE_COLLAPSED_ICON] = $collapsedIcon;
        $this->treeOptions[static::TREE_EXPANDED_ICON] = $expandedIcon;
    }

    public function setNodes(array $nodes): void
    {
        $this->nodes = $nodes;
    }

    public function beforeHtmlRender(): void
    {
        $this->getTemplate()->trySetJs('nodes', Js::array($this->nodes));
        $this->getTemplate()->trySetJs('options', Js::object($this->treeOptions));
        $this->getTemplate()->trySetJs('ptProps', Js::object($this->ptProps));

        $this->createVueApp(self::COMP_NAME, [], $this->getDefaultSelector());
        parent::beforeHtmlRender();
    }
}
