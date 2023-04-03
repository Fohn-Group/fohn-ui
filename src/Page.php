<?php

declare(strict_types=1);

/**
 * The Page View.
 * Render as the HTML page.
 * This is the top view render by App when it is terminating.
 */

namespace Fohn\Ui;

use Fohn\Ui\Js\Jquery;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsChain;
use Fohn\Ui\Js\JsFunction;
use Fohn\Ui\PageLayout\Layout;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Theme\Base;

class Page extends View
{
    public string $defaultTemplate = 'page.html';

    public string $title = '';
    protected ?Layout $layout = null;

    public ?string $toastSelector = '#fohn-toast';
    public string $jsBundleLocation = '/public';

    /** An array of Js packages to include in Page. */
    public array $jsPackages = [
        'jquery' => [
            'url' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js',
        ],
        'fohn-js' => [
            'url' => 'https://unpkg.com/fohn-ui@1.0.1/dist/fohn-ui.min.js',
        ],
    ];

    /** An array of Css packages to include in Page. */
    public array $cssPackages = [
        'flatpickr' => [
            'url' => 'https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.6/flatpickr.min.css',
        ],
        'icons' => [
            'url' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css',
        ],
        'fohn-css' => [
            'url' => 'https://unpkg.com/fohn-ui-css@1.0.0/dist/fohn-ui.min.css',
        ],
    ];

    protected function initRenderTree(): void
    {
        parent::initRenderTree();

        Ui::theme()::styleAs(Base::PAGE, [$this]);
    }

    /**
     * Initializes layout.
     */
    public function addLayout(Layout $layout): self
    {
        // @phpstan-ignore-next-line
        $this->layout = $this->addView($layout);

        return $this;
    }

    public function getLayout(): Layout
    {
        return $this->layout;
    }

    public function beforeHtmlRender(): void
    {
        if ($this->toastSelector) {
            // @phpstan-ignore-next-line
            $this->appendJsAction(JsChain::withUiLibrary()->toastService->enableToastNotification($this->toastSelector));
        }

        parent::beforeHtmlRender();
    }

    /**
     * Render the entire Html page.
     * Update template with page specific content.
     */
    public function outputHtml(string $content = null): string
    {
        $renderView = Ui::viewRenderer($this);
        $template = $renderView->getRenderTemplate();
        $this->includePackagesInTemplate($template);

        $ready = JsFunction::anonymous()->executes($renderView->getJsActions());
        $function = Jquery::withSelf()($ready)->jsRender();
        $template->tryDangerouslyAppendHtml('documentReady', $function);
        $template->trySet('title', $this->title);

        return $template->renderToHtml();
    }

    /**
     * Initialize JS and CSS includes.
     */
    private function includePackagesInTemplate(HtmlTemplate $template): void
    {
        foreach ($this->jsPackages as $package) {
            $tag = Ui::service()->buildHtmlTag('script', [
                'type' => 'application/javascript',
                'src' => $package['url'],
                'defer' => $package['isDefer'] ?? false,
                'async' => $package['isAsync'] ?? false,
            ], '');
            $template->tryDangerouslyAppendHtml('includeJs', $tag . "\n");
        }

        foreach ($this->cssPackages as $package) {
            $tag = Ui::service()->buildHtmlTag('link/', [
                'rel' => 'stylesheet',
                'type' => 'text/css',
                'href' => $package['url'],
                'defer' => $package['isDefer'] ?? false,
            ]);
            $template->tryDangerouslyAppendHtml('includeCss', $tag . "\n");
        }

        // Set js bundle dynamic loading path.
        $template->tryDangerouslySetHtml(
            'InitJsBundle',
            Js::from('window.__fohnBundlePublicPath = {{path}};', ['path' => $this->jsBundleLocation])->jsRender(),
        );
    }

    /**
     * Adds additional JS script include in application template.
     */
    public function includeJsPackage(string $packageName, string $url, bool $isAsync = false, bool $isDefer = false): self
    {
        $this->jsPackages[$packageName] = ['url' => $url, 'isAsync' => $isAsync, 'isDefer' => $isDefer];

        return $this;
    }

    /**
     * Adds additional CSS stylesheet include in application template.
     */
    public function includeCssPackage(string $packageName, string $url): self
    {
        $this->cssPackages[$packageName] = ['url' => $url];

        return $this;
    }
}
