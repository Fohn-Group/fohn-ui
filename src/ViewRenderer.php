<?php

declare(strict_types=1);

/**
 * Render View template as html and View jsActions as javascript.
 *
 * Will call View::beforeHtmlRender method on each View.
 * Will also get all js actions, including children.
 * Also include actions set during View::beforeHtmlRender method call.
 */

namespace Fohn\Ui;

use Fohn\Ui\Js\JsRenderInterface;
use Fohn\Ui\Service\Ui;

class ViewRenderer
{
    /** @var HtmlTemplate A template ready to output html i.e. where children tag has been rendered. */
    private HtmlTemplate $renderTemplate;

    /** @var JsRenderInterface[] */
    private array $viewActions;

    private View $view;

    public function __construct(View $view)
    {
        $this->view = Ui::copyView($view);
        $this->beforeTemplateRender($this->view);

        $this->renderTemplate = $this->getRenderedViewTemplate($this->view);
        $this->viewActions = $this->getJsActionsIncludingChildren($this->view);
    }

    public function getRenderTemplate(): HtmlTemplate
    {
        return $this->renderTemplate;
    }

    public function getHtml(string $region = null): string
    {
        return $this->getRenderTemplate()->renderToHtml($region);
    }

    public function getJsActions(): array
    {
        return $this->viewActions;
    }

    public function getJavascript(): string
    {
        $renderedActions = [];

        foreach ($this->getJsActions() as $action) {
            if ($rendered = $action->jsRender()) {
                $renderedActions[] = $rendered;
            }
        }

        return trim(preg_replace('~(;;)~', ';' . \PHP_EOL, implode(';', $renderedActions)));
    }

    /**
     * Execute View::beforeHtmlRender method for a View and all it's children.
     */
    private function beforeTemplateRender(View $view): void
    {
        \Closure::bind(function () use ($view) {
            $view->beforeHtmlRender();
        }, null, View::class)();

        foreach ($view->getViewElements() as $innerView) {
            $this->beforeTemplateRender($innerView);
        }
    }

    /**
     * Return JsActions for this view and all it's children.
     */
    private function getJsActionsIncludingChildren(View $view): array
    {
        return array_merge($view->getJsActions(), $this->getAllChildrenActions($view));
    }

    /**
     * Return all actions set in children of the view.
     */
    private function getAllChildrenActions(View $view, array $actions = []): array
    {
        foreach ($view->getViewElements() as $innerView) {
            $actions = array_merge($actions, $innerView->getJsActions());
            foreach ($innerView->getViewElements() as $innerInnerView) {
                $actions = array_merge($actions, $this->getAllChildrenActions($innerInnerView, $innerInnerView->getJsActions()));
            }
        }

        return $actions;
    }

    /**
     * Return a view rendered HtmlTemplate.
     */
    private function getRenderedViewTemplate(View $view): HtmlTemplate
    {
        foreach ($view->getViewElements() as $innerView) {
            $this->getRenderedViewTemplate($innerView);
        }

        return $this->renderTemplate($view);
    }

    /**
     * Return an HtmlTemplate of a View where all inners view has been rendered as html and
     * append into parent View region.
     */
    private function renderTemplate(View $view): HtmlTemplate
    {
        $template = clone $view->getTemplate();
        foreach ($view->getViewElements() as $innerView) {
            $template->dangerouslyAppendHtml($innerView->templateRegion, $this->renderTemplate($innerView)->renderToHtml());
        }

        return $template;
    }
}
