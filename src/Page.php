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
use Fohn\Ui\Page\Layout;
use Fohn\Ui\Page\Package;
use Fohn\Ui\Service\Theme\Base;
use Fohn\Ui\Service\Ui;

class Page extends View
{
    public const TOKEN_KEY_NAME = '_csfr_token';
    public const JS_PACKAGE_TAG_REGION = 'includeJs';
    public const CSS_PACKAGE_TAG_REGION = 'includeCss';
    public string $defaultTemplate = 'page.html';

    public string $title = '';
    protected ?Layout $layout = null;

    /** Used a specific js package version, ex: '1.5.0', a wildcard or leave empty for latest. */
    public string $fohnJsVersion = '^1';
    public string $fohnCssVersion = '^2';
    public string $jQueryVersion = '^3';

    public string $toastSelector = '#fohn-toast';
    public string $jsBundleLocation = '/public';
    public string $flatPickrCssUrl = 'https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.6/flatpickr.min.css';
    public string $bootStrapIconsUrl = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css';
    public string $fohnCssBaseUrl = 'https://unpkg.com/fohn-ui-css@';
    public string $fohnJsBaseUrl = 'https://unpkg.com/fohn-ui@';

    /** @var array<string, Package> */
    protected array $externalPackages = [];

    public array $metaTags = [];

    public function __construct()
    {
        parent::__construct();
        $this->includePackages();
    }

    /**
     * Include necessary Javascript/Css external package need to run the page.
     */
    protected function includePackages(): void
    {
        $this->includePackage('jQuery', Package::addScript('https://unpkg.com/jquery@' . $this->jQueryVersion));
        $this->includePackage('fohn-js', Package::addScript($this->fohnJsBaseUrl . $this->fohnJsVersion));
        $this->includePackage('flatPickrCss', Package::addStylesheet($this->flatPickrCssUrl));
        $this->includePackage('bootstrapIcon', Package::addStylesheet($this->bootStrapIconsUrl));
        $this->includePackage('fohn-css', Package::addStylesheet($this->fohnCssBaseUrl . $this->fohnCssVersion));
    }

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
        foreach ($this->externalPackages as $name => $package) {
            $template->tryDangerouslyAppendHtml($package->getPageRegion(), $package->getHtmlTag() . "\n");
        }

        // Set js bundle dynamic loading path.
        $template->tryDangerouslySetHtml(
            'InitJsBundle',
            Js::from('window.__fohnBundlePublicPath = {{path}};', ['path' => $this->jsBundleLocation])->jsRender(),
        );
    }

    public function includePackage(string $packageName, Package $package): void
    {
        $this->externalPackages[$packageName] = $package;
    }
}
