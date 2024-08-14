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
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Tw;
use Fohn\Ui\View;
use Fohn\Ui\View\Button;

class Tree extends View implements VueInterface
{
    use HookTrait;
    use VueTrait;

    public const TREE_SELECTED_COLOR = 'selectedColors';
    public const TREE_HOVER_COLOR = 'hoverColors';
    public const TREE_COLLAPSED_ICON = 'collapsedIcon';
    public const TREE_EXPANDED_ICON = 'expandedIcon';

    public const POST_BTN_REGION_NAME = 'PostBtn';

    public const HOOK_NODE_CHANGED = self::class . '@changeNode';
    public const HOOK_TREE_POST = self::class . '@treePost';

    public string $selectedColor = 'info';

    public string $defaultTemplate = 'vue-component/tree.html';

    public array $defaultTailwind = [
        'border',
        'border-gray-300',
        'rounded-md',
    ];

    public array $postBtnSeed = [Button::class, 'label' => 'Save', 'color' => 'primary'];
    protected ?Button $postBtn = null;
    protected string $postBtnUiName = 'postBtn';

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

    protected array $nodeValue = [];

    protected array $treeOptions = [];

    protected ?Ajax $treeNodeChangedRequest = null;
    protected ?Ajax $treePostRequest = null;

    protected function initRenderTree(): void
    {
        parent::initRenderTree();

        $this->setColorsOptions(implode(' ', Fohn::getColorRecipes($this->selectedColor)), 'hover:bg-' . TwConstant::COLORS['neutral-light']);
        $this->setIconOptions('bi bi-caret-right', 'bi bi-caret-down');
    }

    protected function initTreeNodeChangedRequest(\Closure $fx): void
    {
        if (!$this->treeNodeChangedRequest) {
            $this->treeNodeChangedRequest = Ajax::addAbstractTo($this);
            $this->onHook(self::HOOK_NODE_CHANGED, $fx);
        }
    }

    protected function initTreePostRequest(\Closure $fx): void
    {
        if (!$this->treePostRequest) {
            $this->treePostRequest = Ajax::addAbstractTo($this);
            $this->onHook(self::HOOK_TREE_POST, $fx);
        }
    }

    public function setValue(array $value): void
    {
        $this->nodeValue = $value;
    }

    public function onTreePost(\Closure $fx, Button $btn = null): void
    {
        $this->initTreePostRequest($fx);

        $this->treePostRequest->onAjaxPostRequest(function (array $payload): JsRenderInterface {
            // an array of key values for all selected nodes.
            $nodeKeys = $payload['__nodeKeys'] ?? [];
            // the raw value for Tree. Save this array value in order to restore selected state using Tree::setValue method.
            $treeValue = $payload['__treeValue'] ?? [];

            return $this->callHook(self::HOOK_TREE_POST, HookFn::withJsRenderInterface([$nodeKeys, $treeValue, $this]));
        });

        if (!$this->postBtn) {
            $this->initPostBtn($btn);
        }
    }

    public function getPostBtn(): Button
    {
        if (!$this->postBtn) {
            $this->initPostBtn(null);
        }

        return $this->postBtn;
    }

    public function initPostBtn(?Button $btn, string $regionName = self::POST_BTN_REGION_NAME): void
    {
        if (!$btn) {
            /** @var Button $btn */
            $btn = Ui::factoryFromSeed($this->postBtnSeed);
        }

        $btn->setViewName($this->postBtnUiName);
        static::bindVueEvent($btn, 'click', 'postValue');
        static::bindVueAttr($btn, 'disabled', 'isFetching || !canFetch');
        static::bindVueAttr($btn, 'class', '{loading: isFetching}');
        $this->addView($btn, $regionName);
        $this->postBtn = $btn;
    }

    /**
     * Function $fx to be executed when Tree node selection changed.
     * The callback function ($fx) must return a jsRenderInterface.
     */
    public function onTreeNodeChanged(\Closure $fx): void
    {
        $this->initTreeNodeChangedRequest($fx);

        $this->treeNodeChangedRequest->onAjaxPostRequest(function (array $payload): JsRenderInterface {
            // Weather a node was select or unselect.
            $nodeAction = $payload['__nodeAction'] ?? null;
            // the key to the select/unselect node.
            $hitNode = $payload['__nodeKey'] ?? null;

            return $this->callHook(self::HOOK_NODE_CHANGED, HookFn::withJsRenderInterface([$nodeAction, $hitNode, $this]));
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
        $this->getTemplate()->trySetJs('nodeValue', Js::object($this->nodeValue));
        $this->getTemplate()->trySetJs('nodeChangedUrl', Js::string($this->treeNodeChangedRequest ? $this->treeNodeChangedRequest->getUrl() : ''));
        $this->getTemplate()->trySetJs('postUrl', Js::string($this->treePostRequest ? $this->treePostRequest->getUrl() : ''));
        $this->getTemplate()->trySetJs('postBtnName', Js::string($this->postBtnUiName));

        $this->createVueApp(self::COMP_NAME, [], $this->getDefaultSelector());
        parent::beforeHtmlRender();
    }
}
