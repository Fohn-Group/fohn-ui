<?php

declare(strict_types=1);

namespace Fohn\Ui;

use Fohn\Ui\Core\ViewHelperTrait;
use Fohn\Ui\Js\JsRenderInterface;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Tw;

/**
 * Implements a most core view, which all the others components descend
 * form.
 */
class View extends AbstractView
{
    use ViewHelperTrait;

    public const MAIN_TEMPLATE_REGION = HtmlTemplate::MAIN_TEMPLATE_TAG;
    public const AFTER_TEMPLATE_REGION = HtmlTemplate::AFTER_TEMPLATE_TAG;
    public const BEFORE_TEMPLATE_REGION = HtmlTemplate::BEFORE_TEMPLATE_TAG;

    protected const CLASS_TEMPLATE_TAG = 'classAttr';
    protected const ID_TEMPLATE_TAG = 'idAttr';
    protected const STYLE_TEMPLATE_TAG = 'styleAttr';
    protected const ATTR_TEMPLATE_TAG = 'attributes';
    protected const TAG_TEMPLATE_TAG = 'htmlTag';

    /** @var JsRenderInterface[] */
    private array $jsActions = [];

    /** Name of the region in the parent's template where this object will output itself. */
    public string $templateRegion = self::MAIN_TEMPLATE_REGION;

    /** The html id attribute. */
    private string $idAttribute = '';

    /** The html data-ui-name attribute. */
    protected ?string $viewName = null;

    /** Container for the HTML class attribute. Usually not related to tailwind utility name. */
    public array $cssClasses = [];

    /** @var string[] Tailwind utility class name container. */
    private array $tws = [];
    /** @var string[] Utilities added here will be removed before final view rendering. */
    private array $twRemoveContainer = [];

    /** @var string[] default tailwind utilities. */
    public array $defaultTailwind = [];

    /** Container for css styles attributes. */
    public array $htmlStyles = [];

    /** Containers for html attribute => value. */
    protected array $htmlAttributes = [];

    /** @var string[] stickyGet url arguments */
    private array $stickyArgs = [];

    /**
     * Template object, that, for most Views will be rendered to
     * produce HTML output. If you leave this object as "null" then
     * a new Template will be generated during init() based on the
     * value of $defaultTemplate.
     */
    protected ?HtmlTemplate $template = null;

    public string $defaultTemplate = 'view/element.html';

    /** Set text content of main region. */
    private ?string $textContent = null;

    /** Default html tag. */
    public string $htmlTag = 'div';

    public function __construct(array $defaults = [])
    {
        parent::__construct($defaults);
        $this->setTailwinds($this->defaultTailwind);
        $this->initViewName();
        $this->initTemplate();
    }

    /**
     * Called when view becomes part of render tree, i.e. View::add() into another View.
     * You can override it but avoid placing any "heavy processing" here.
     */
    protected function initRenderTree(): void
    {
        parent::initRenderTree();
        $this->setIdAttribute(Ui::service()->factoryId($this->getViewId()));
    }

    /**
     * Set view name.
     * Override this method to apply your own view name algorithm
     * when View is instantiate.
     */
    protected function initViewName(): void
    {
        if (!$this->viewName) {
            $this->setViewName(Ui::service()->factoryViewName(static::class));
        }
    }

    /**
     * Set view template.
     * Override this method to apply your own template algorithm
     * when View is instantiate.
     */
    protected function initTemplate(): void
    {
        if (!$this->template) {
            $this->setTemplate(Ui::templateFromFile($this->defaultTemplate));
        }
    }

    public function getTemplate(): ?HtmlTemplate
    {
        return $this->template;
    }

    public function setTemplate(HtmlTemplate $template): void
    {
        $this->template = $template;
    }

    public function getTws(): array
    {
        return $this->tws;
    }

    public function getIdAttribute(): string
    {
        return $this->idAttribute;
    }

    public function setIdAttribute(string $id): self
    {
        $this->idAttribute = $id;

        return $this;
    }

    public function getViewName(): string
    {
        return $this->viewName;
    }

    public function setViewName(string $name): self
    {
        $this->viewName = $name;

        return $this;
    }

    /**
     * Set main region with text.
     * In order to use html markup, $useHtmlSpecialChars needs to be false.
     * textContent is set in template at rendering if set.
     */
    public function setTextContent(?string $text, bool $useHtmlSepcialChars = true): self
    {
        $this->textContent = ($text && $useHtmlSepcialChars) ? Ui::service()->htmlSpecialChars($text) : $text;

        return $this;
    }

    public function getTextContent(): ?string
    {
        return $this->textContent;
    }

    /**
     * Sets View html tag element.
     */
    public function setHtmlTag(string $htmlTag): self
    {
        $this->htmlTag = $htmlTag;

        return $this;
    }

    public function setTailwinds(array $tws): void
    {
        $this->tws = $tws;
    }

    public function removeTailwind(string $tw): self
    {
        $this->twRemoveContainer[] = $tw;

        return $this;
    }

    /**
     * Remove Tw utility set.
     */
    private function removeFromTwContainer(): void
    {
        foreach ($this->twRemoveContainer as $tw) {
            $this->tws = Tw::from($this->tws)->filter(function (string $utitlity) use ($tw) {
                return $utitlity !== $tw;
            })();
        }
    }

    public function appendTailwind(string $tw): self
    {
        $this->appendTailwinds([$tw]);

        return $this;
    }

    public function appendTailwinds(array $tws): self
    {
        $this->tws = Tw::from($this->tws)->merge($tws)();

        return $this;
    }

    /**
     * Makes view into a "<a>" element with a link.
     */
    public function linkTo(string $url, string $target = '_self'): self
    {
        $this->htmlTag = 'a';
        $this->appendHtmlAttribute('href', $url);
        $this->appendHtmlAttribute('target', $target);

        return $this;
    }

    /**
     * Add View inside a template region.
     */
    public function addView(self $view, string $region = self::MAIN_TEMPLATE_REGION): self
    {
        $this->addAbstractView($view->injectDefaults(['templateRegion' => $region]), self::VIEW_CONTAINER);

        return $view;
    }

    /**
     * Add CSS class to element. Previously added classes are not affected.
     * Multiple CSS classes can also be added if passed as space separated
     * string or array of class names.
     */
    public function appendCssClasses(string $class): self
    {
        $this->cssClasses = array_merge($this->cssClasses, explode(' ', $class));

        return $this;
    }

    /**
     * Remove one or several CSS classes from the element.
     */
    public function removeCssClasses(string $class): self
    {
        $this->cssClasses = array_diff($this->cssClasses, explode(' ', $class));

        return $this;
    }

    /**
     * Add inline CSS style to element.*.
     */
    public function appendHtmlStyle(string $property, string $style = null): self
    {
        $this->htmlStyles[$property] = $style;

        return $this;
    }

    public function appendHtmlStyles(array $styles): self
    {
        foreach ($styles as $property => $value) {
            $this->appendHtmlStyle($property, $value);
        }

        return $this;
    }

    public function removeHtmlStyle(string $property): self
    {
        unset($this->htmlStyles[$property]);

        return $this;
    }

    public function appendHtmlAttribute(string $attribute, string $value = null): self
    {
        if ($value || $value === null) {
            $this->htmlAttributes[$attribute] = $value;
        }

        return $this;
    }

    public function appendHtmlAttributes(array $attributes): self
    {
        foreach ($attributes as $attribute => $value) {
            $this->appendHtmlAttribute($attribute, $value);
        }

        return $this;
    }

    public function removeHtmlAttribute(string $attribute): self
    {
        unset($this->htmlAttributes[$attribute]);

        return $this;
    }

    /**
     * Default rendering of Tw utilities.
     */
    protected function renderTailwind(string $output, string $utility): string
    {
        return $output . ' ' . $utility;
    }

    /**
     * View-specific rendering stuff.
     * This method is called when this view is about
     * to be rendered as Html content.
     * This is a good place to set last minute template content.
     */
    protected function beforeHtmlRender(): void
    {
        $css = '';
        if ($this->getTws()) {
            $this->removeFromTwContainer();
            $css = trim(Tw::from($this->getTws())->toString(\Closure::fromCallable([$this, 'renderTailwind'])));
        }

        if ($this->cssClasses) {
            $css .= $css ? ' ' : '';
            $css .= trim(implode(' ', $this->cssClasses));
        }

        $this->getTemplate()->trySet(self::CLASS_TEMPLATE_TAG, $css);
        $this->getTemplate()->trySet(self::TAG_TEMPLATE_TAG, $this->htmlTag);

        if ($this->textContent !== null) {
            $this->getTemplate()->tryDangerouslySetHtml(self::MAIN_TEMPLATE_REGION, $this->textContent);
        }

        $this->renderStyles();
        $this->renderHtmlAttributes();
    }

    protected function renderHtmlAttributes(): void
    {
        $this->appendHtmlAttribute('data-ui-name', $this->getViewName());
        $this->getTemplate()->trySet(self::ID_TEMPLATE_TAG, $this->getIdAttribute());

        $attributes = '';
        foreach ($this->htmlAttributes as $attr => $val) {
            $attributes .= trim($attr) . '="' . trim((string) $val) . '" ';
        }
        if ($attributes) {
            $this->getTemplate()->trySet(self::ATTR_TEMPLATE_TAG, trim($attributes));
        }
    }

    protected function renderStyles(): void
    {
        if ($this->htmlStyles) {
            $styles = '';
            foreach ($this->htmlStyles as $style => $val) {
                $styles .= "{$style}:{$val}; ";
            }
            $this->getTemplate()->trySet(self::STYLE_TEMPLATE_TAG, trim($styles));
        }
    }

    /**
     * Render View using json format.
     */
    public function renderToJsonArr(string $region = null): array
    {
        $renderer = Ui::viewRenderer($this);

        return [
            'success' => true,
            'message' => 'Success',
            'jsRendered' => $renderer->getJavascript(),
            'html' => $renderer->getHtml($region),
            'id' => $this->getIdAttribute(),
        ];
    }

    /**
     * Output this view as html content.
     */
    public function getHtml(bool $includeJs = false): string
    {
        $renderer = Ui::viewRenderer($this);
        $js = $includeJs ? (\PHP_EOL . Ui::service()->buildHtmlTag('script', ['type' => 'application/javascript'], \PHP_EOL . '  ' . $renderer->getJavascript() . \PHP_EOL)) : '';

        return $renderer->getHtml() . $js;
    }

    /**
     * Add a Javascript Action to this view.
     * Javascript Actions are rendered when View are fully rendered.
     */
    public function appendJsAction(JsRenderInterface $action): self
    {
        $this->jsActions[] = $action;

        return $this;
    }

    public function appendJsActions(array $actions): void
    {
        foreach ($actions as $action) {
            $this->appendJsAction($action);
        }
    }

    public function getJsActions(): array
    {
        return $this->jsActions;
    }

    /**
     * Add an action on top of $jsActions array, in order for it to render first.
     */
    public function unshiftJsActions(JsRenderInterface $action): self
    {
        array_unshift($this->jsActions, $action);

        return $this;
    }

    /**
     * Return jsActions render as javascript.
     */
    public function getJavascript(): string
    {
        return Ui::viewRenderer($this)->getJavascript();
    }

    public function getUrlStickyArgs(): array
    {
        return $this->getStickyArgs();
    }

    private function getStickyArgs(): array
    {
        if ($this->issetOwner()) {
            $stickyArgs = array_merge($this->getOwner()->getStickyArgs(), $this->stickyArgs);
        } else {
            $stickyArgs = $this->stickyArgs;
        }

        return $stickyArgs;
    }

    /**
     * Check for GET argument and add it to stickyArgs.
     *
     * If GET argument is empty or false, it won't make it sticky.
     *
     * If GET argument is not presently set you can specify a 2nd argument
     * to forge-set the GET argument for current view, and it's sub-views.
     */
    public function stickyGet(string $name, string $newValue = null): ?string
    {
        $this->stickyArgs[$name] = $_GET[$name] ?? $newValue;

        return $this->stickyArgs[$name];
    }

    public function removeStickyGet(string $name): self
    {
        unset($this->stickyArgs[$name]);

        return $this;
    }
}
