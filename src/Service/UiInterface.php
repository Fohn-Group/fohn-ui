<?php

declare(strict_types=1);

/**
 * Application service interface.
 */

namespace Fohn\Ui\Service;

use Fohn\Ui\App;
use Fohn\Ui\Component\Form\Layout\FormLayoutInterface;
use Fohn\Ui\HtmlTemplate;
use Fohn\Ui\Page;
use Fohn\Ui\PageLayout\Layout;
use Fohn\Ui\Tailwind\Theme\ThemeInterface;
use Fohn\Ui\View;
use Fohn\Ui\ViewRenderer;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface AppServiceInterface.
 *
 * @method modelCtrl()
 */
interface UiInterface
{
    public static function service(): self;

    /**
     * Get currently running App.
     */
    public static function app(): App;

    public static function theme(): ThemeInterface;

    public static function page(): Page;

    public static function serverRequest(): ServerRequestInterface;

    public static function timezone(string $timezone = null): string;

    /** Get current Url as request by the client, without query. */
    public static function parseRequestUrl(): string;

    /** Build an url with query params. */
    public static function buildUrl(string $url, array $params = []): string;

    /** Return a layout View object. Layout is the main page content. */
    public static function layout(): Layout;

    public static function viewRenderer(View $view): ViewRenderer;

    public static function templateFromFile(string $fileName): HtmlTemplate;

    public static function renderException(\Throwable $exception): string;

    public static function bindVueEvent(View $view, string $eventName, string $event): void;

    /**
     * Create View objects using seed.
     *
     * @return mixed
     */
    public static function factoryFromSeed(array $seed);

    /**
     * Create View objects base on their class name.
     *
     * @return mixed
     */
    public static function factory(string $className, array $properties = []);

    public function boot(\Closure $fx): void;

    public function setApp(App $app): App;

    /** Set Page use by App output handler.*/
    public function initAppPage(Page $page): Page;

    /** Where to look for html templates files. */
    public function appendTemplateDirectories(array $directories): void;

    /** Generate View::name property. */
    public function factoryViewName(string $className): string;

    /** Generate View::attributeId property. */
    public function factoryId(string $viewName): string;

    public function htmlSpecialChars(string $html): string;

    /** Return a Form Layout Interface. Called by Form for its default layout. */
    public function getFormLayout(): FormLayoutInterface;

    /**
     * Generate Html tag.
     *
     * @param string|array $tag
     * @param string|array $attributes
     * @param string|array $value
     */
    public function buildHtmlTag($tag = null, $attributes = null, $value = null): string;

    /**
     * @param mixed $data
     */
    public function encodeJson($data, bool $forceObject = false): string;

    public function decodeJson(string $json, bool $isAssociative = true): array;

    public function terminateJson(array $output, int $statusCode = 200): void;

    /** Merge View seeds. */
    public function mergeSeeds(array ...$seed): array;

    public function isAjaxRequest(): bool;

    /** Create a deep copy of a View. */
    public static function copyView(View $view): View;

    public function renderExceptionAsHtml(\Throwable $exception): string;

    public function setExceptionHandler(Page $page): void;
}
