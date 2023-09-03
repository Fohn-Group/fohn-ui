<?php

declare(strict_types=1);

/**
 * The Page View.
 * Render as the HTML page.
 * This is the top view render by App when it is terminating.
 */

namespace Fohn\Ui;

use Fohn\Ui\Callback\Request;
use Fohn\Ui\Core\Utils;
use Fohn\Ui\Js\Jquery;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsChain;
use Fohn\Ui\Js\JsFunction;
use Fohn\Ui\PageLayout\Layout;
use Fohn\Ui\Service\Theme\Base;
use Fohn\Ui\Service\Ui;

class Page extends View
{
    public const TOKEN_KEY_NAME = '_csfr_token';
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
            'url' => 'https://unpkg.com/fohn-ui@1.4.0/dist/fohn-ui.min.js',
        ],
    ];

    /** An array of Css packages to include in Page. */
    public array $cssPackages = [
        'flatpickr' => [
            'url' => 'https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.6/flatpickr.min.css',
        ],
        'icons' => [
            'url' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css',
        ],
        'fohn-css' => [
            'url' => 'https://unpkg.com/fohn-ui-css@1.3.0/dist/fohn-ui.min.css',
        ],
    ];

    public array $metaTags = [];

    protected function initRenderTree(): void
    {
        parent::initRenderTree();

        Ui::theme()::styleAs(Base::PAGE, [$this]);
    }

    /**
     * Protect all callback request, coming from this page, from CSFR attack.
     */
    public function csfrProtect(string $secret, string $redirectTo = null, int $strength = 16): void
    {
        Request::protect($redirectTo);

        if (!Ui::service()->isAjaxRequest()) {
            $csfrToken = Utils::generateToken($secret, $strength);
            $this->appendMetaTag(Ui::service()->buildHtmlTag('meta', ['name' => 'csfr-token', 'content' => $csfrToken]));
            Ui::session()->set(static::TOKEN_KEY_NAME, $csfrToken);
        }
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

    protected function beforeHtmlRender(): void
    {
        if ($this->toastSelector) {
            // @phpstan-ignore-next-line
            $this->appendJsAction(JsChain::withUiLibrary()->toastService->enableToastNotification($this->toastSelector));
        }

        foreach ($this->metaTags as $htmlTag) {
            $this->getTemplate()->tryDangerouslyAppendHtml('meta', $htmlTag);
        }

        parent::beforeHtmlRender();
    }

    public function appendMetaTag(string $htmlTag): void
    {
        $this->metaTags[] = $htmlTag;
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
