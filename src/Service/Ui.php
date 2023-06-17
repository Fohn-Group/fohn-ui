<?php

declare(strict_types=1);

/**
 * Ui Service
 * Setup and dispatch proper Ui components
 * like App, View, Page, ViewRenderer, Theme etc.
 * Views will call Ui service functionalities instead of calling Class method directly.
 * This allows anyone to implement their own functionalities.
 */

namespace Fohn\Ui\Service;

use Fohn\Ui\AbstractView;
use Fohn\Ui\App;
use Fohn\Ui\Callback\Request;
use Fohn\Ui\Component\Form\Layout\FormLayoutInterface;
use Fohn\Ui\Component\Form\Layout\Standard;
use Fohn\Ui\Core\Exception;
use Fohn\Ui\Core\ExceptionRenderer\Html;
use Fohn\Ui\Core\Utils;
use Fohn\Ui\HtmlTemplate;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsChain;
use Fohn\Ui\Js\JsRenderInterface;
use Fohn\Ui\Js\Type\Type;
use Fohn\Ui\Page;
use Fohn\Ui\PageLayout\Layout;
use Fohn\Ui\Tailwind\Theme\Fohn;
use Fohn\Ui\Tailwind\Theme\ThemeInterface;
use Fohn\Ui\View;
use Fohn\Ui\ViewRenderer;
use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\ServerRequestInterface;

class Ui implements UiInterface
{
    protected static ?Ui $instance = null;

    public const DEV_ENV = 'dev';
    public const PROD_ENV = 'production';
    public const TEST_ENV = 'test';

    public const TOKEN_KEY_NAME = '_csfr_token';

    /** AJAX custom header key-pair value as set in apiService. */
    public const AJAX_HEADER_KEY = 'x-custom-header';
    public const AJAX_HEADER_VALUE = '__fohn-ajax-request';
    public const EXCEPTION_OUTPUT_KEY = 'exceptionHtml';

    public const DUMP_PARAM_NAME = 'dump';

    /** The Js library package name. */
    public string $jsLibrary = 'fohn';
    public string $environment = self::PROD_ENV;

    public string $title = 'Fohn Application';
    public bool $catchRunawayCallbacks = true;
    public string $themeClass = Fohn::class;
    public string $templateEngineClass = HtmlTemplate::class;
    public string $rendererClass = ViewRenderer::class;
    public array $formLayoutSeed = [Standard::class];
    public string $sessionServiceClass = Session::class;

    public string $timezone = 'UTC';
    public string $locale = 'en_CA';
    public array $displayformat = [
        'currency_code' => 'CAD',
        'currency' => '$',
        'date' => 'M d, Y',
        'time' => 'H:i',
        'datetime' => 'M d, Y H:i:s',
    ];

    /** The top view of the app. */
    private Page $page;
    private App $app;
    private array $templateDirectories = [];
    private ?ServerRequestInterface $serverRequest = null;

    private bool $isBooted = false;

    final private function __construct()
    {
        $this->templateDirectories[] = dirname(
            __DIR__,
            2
        ) . \DIRECTORY_SEPARATOR . 'template' . \DIRECTORY_SEPARATOR . 'tailwind';
    }

    public static function service(): self
    {
        if (static::$instance === null) {
            static::$instance = new static();
            static::serverRequest();
        }

        return static::$instance;
    }

    public static function session(): SessionInterface
    {
        /** @var Session $class */
        $class = static::service()->sessionServiceClass;

        return $class::getInstance();
    }

    public static function getDisplayFormat(string $name): string
    {
        return static::service()->displayformat[$name];
    }

    public static function theme(): ThemeInterface
    {
        /** @var ThemeInterface $class */
        $class = static::service()->themeClass;

        return $class::getInstance();
    }

    public static function app(): App
    {
        return static::service()->app;
    }

    public static function page(): Page
    {
        return static::service()->page;
    }

    public static function layout(): Layout
    {
        return static::service()->page->getLayout();
    }

    public static function serverRequest(): ServerRequestInterface
    {
        if (!static::service()->serverRequest) {
            static::service()->serverRequest = ServerRequest::fromGlobals();
        }

        return static::service()->serverRequest;
    }

    public static function locale(string $locale = null, int $option = \LC_ALL): string
    {
        if ($locale) {
            setlocale($option, $locale);
            static::service()->locale = $locale;
        }

        return static::service()->locale;
    }

    public static function timezone(string $timezone = null): string
    {
        if ($timezone) {
            static::service()->timezone = $timezone;
        }

        return static::service()->timezone;
    }

    /**
     * Factory method when seed is provide.
     * $seed[0] must contains the class name.
     * The rest of the seed determine object properties.
     *
     * @return mixed
     */
    public static function factoryFromSeed(array $seed)
    {
        // determine class name
        if (!isset($seed[0])) {
            throw new Exception('Trying to factory from seed but $seed[0] is not set.');
        }
        if (!is_string($seed[0])) {
            throw (new Exception(
                'Trying to factory from seed but $seed[0] is not a class name (string).'
            ))->addMoreInfo('$seed[0]', $seed[0]);
        }

        $className = $seed[0];
        unset($seed[0]);
        static::service()->assertIsSubClassOfAbsctractView($className);

        return new $className($seed);
    }

    public function getFormLayout(): FormLayoutInterface
    {
        return static::factoryFromSeed($this->formLayoutSeed);
    }

    /**
     * Create a View base on their class name.
     *
     * @return mixed
     */
    public static function factory(string $className, array $properties = [])
    {
        static::service()->assertIsSubClassOfAbsctractView($className);

        return new $className($properties);
    }

    /**
     * Create Url from a ServerRequest path and add $params as Get query.
     */
    public static function parseRequestUrl(): string
    {
        return static::service()->serverRequest()->getUri()->getPath();
    }

    public static function buildUrl(string $url, array $params = []): string
    {
        return (string) (new Uri($url))->withQuery(Query::build($params));
    }

    /**
     * Setup service via Closure.
     */
    public function boot(\Closure $fx): void
    {
        if (!$this->isBooted) {
            $fx($this);
            $this->isBooted = true;
        }
    }

    public function appendTemplateDirectories(array $directories): void
    {
        $this->templateDirectories = array_merge($this->templateDirectories, $directories);
    }

    public static function templateFromFile(string $fileName): HtmlTemplate
    {
        return static::service()->factoryTemplateFromFile($fileName);
    }

    public static function renderException(\Throwable $exception): string
    {
        return static::service()->renderExceptionAsHtml($exception);
    }

    public function setApp(App $app): App
    {
        $this->app = $app;

        return $app;
    }

    public static function isBooted(): bool
    {
        return static::service()->isBooted;
    }

    public function markAsBooted(): void
    {
        $this->isBooted = true;
    }

    public function initAppPage(Page $page): Page
    {
        $page->invokeInitRenderTree();
        $this->page = $page;

        $this->app->setOutputHandler(function () use ($page): string {
            $output = $page->outputHtml();
            if ($this->catchRunawayCallbacks) {
                Request::assertNoCallbackRunning();
            }

            return $output;
        });

        return $page;
    }

    public static function viewRenderer(View $view): ViewRenderer
    {
        $rendererClass = static::service()->rendererClass;

        return new $rendererClass($view);
    }

    public function factoryViewName(string $className): string
    {
        return Utils::getFromClassName($className);
    }

    public function factoryId(string $viewName, string $keep = '', int $lenght = 10): string
    {
        return Utils::generateId($viewName, $keep, $lenght);
    }

    public function sanitize(string $html): string
    {
        return htmlspecialchars($html, \ENT_NOQUOTES | \ENT_HTML5, 'UTF-8');
    }

    public function getInput(): array
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    /**
     * @param string|array $tag
     * @param string|array $attributes
     * @param string|array $value
     */
    public function buildHtmlTag($tag = null, $attributes = null, $value = null): string
    {
        return Utils::buildTag($tag, $attributes, $value);
    }

    public function hasValidOptions(array $options, array $validKeys): bool
    {
        return Utils::hasValidOptions($options, $validKeys);
    }

    /**
     * @param mixed $data
     */
    public function encodeJson($data, bool $forceObject = false): string
    {
        return Utils::encodeJson($data, $forceObject);
    }

    public function decodeJson(string $json, bool $isAssociative = true): array
    {
        return Utils::decodeJson($json, $isAssociative);
    }

    public function mergeSeeds(array ...$seed): array
    {
        return Utils::mergeSeeds(...$seed);
    }

    public function setExceptionHandler(Page $page): void
    {
        set_exception_handler(function (\Throwable $exception) use ($page): void {
            if ($this->isAjaxRequest()) {
                static::app()->terminateJson(['success' => false, static::EXCEPTION_OUTPUT_KEY => static::service()->renderExceptionAsHtml($exception)], 500);
            } else {
                try {
                    $page->invokeInitRenderTree();
                    $template = static::viewRenderer($page)->getRenderTemplate();
                    $template->dangerouslySetHtml('Content', $this->renderExceptionAsHtml($exception));
                    static::app()->terminateHtml($template->renderToHtml(), 500);
                } catch (\Throwable $e) {
                    static::app()->terminateHtml($this->renderExceptionAsHtml($exception), 500);
                }
            }
        });

        set_error_handler(
            static function (int $severity, string $msg, string $file, int $line): bool {
                throw new \ErrorException($msg, 0, $severity, $file, $line);
            },
            \E_ALL
        );
    }

    public function isAjaxRequest(): bool
    {
        return in_array(static::AJAX_HEADER_VALUE, static::serverRequest()->getHeader(static::AJAX_HEADER_KEY), true);
    }

    public function terminateJson(array $output, int $statusCode = 200): void
    {
        static::app()->terminateJson($output, $statusCode);
    }

    /**
     * A convenient wrapper for sending user to another page.
     */
    public static function redirect(string $url, bool $permanent = false): void
    {
        $response = new Response();
        $response = $response->withHeader('Location', $url)->withStatus($permanent ? 301 : 302);
        static::app()->terminateAndExit($response);
    }

    public static function jsWindowOpen(string $url, array $params = [], string $target = '_blank'): JsRenderInterface
    {
        // @phpstan-ignore-next-line
        return JsChain::with(static::service()->jsLibrary)->utils()->browser()->windowOpen($url, Type::factory($params), Type::factory($target));
    }

    public static function jsRedirect(string $url, array $param = []): JsRenderInterface
    {
        // @phpstan-ignore-next-line
        return JsChain::with(static::service()->jsLibrary)->utils()->browser()->redirect($url, Type::factory($param));
    }

    /**
     * Bind a Vue v-on:event to a View via html attributes.
     */
    public static function bindVueEvent(View $view, string $eventName, string $event): void
    {
        if (!$view->getTemplate()->hasTag('attributes')) {
            throw new Exception('Unable to bind Vue event. Template for View does not have the attributes tag.');
        }
        $view->appendHtmlAttribute('v-on:' . $eventName, $event);
    }

    public static function copyView(View $view): View
    {
        /** @var View $viewCopy */
        $viewCopy = Utils::copy($view);

        return $viewCopy;
    }

    /**
     * Dump (echo) a render template view to a page without any Js.
     * Useful for debugging a Vue component when combining multiple Views
     * that uses different templates in a single component.
     * It allows to visualize the php rendered template before it get render by Vue.
     */
    public static function viewDump(View $view, string $dumpWhen, bool $includeJs = true): void
    {
        $needDump = $_GET[self::DUMP_PARAM_NAME] ?? null;
        if ($needDump) {
            if ($needDump === $dumpWhen) {
                static::app()->terminateHtml(static::service()->getDumpPageHtml($view, $includeJs));
            }
        }
    }

    protected function getDumpPageHtml(View $view, bool $includeJs): string
    {
        /** @var Page $page */
        $page = Page::factory(['title' => 'Fohn-ui View debugger']);
        $page->invokeInitRenderTree();
        $page->addView(View\Heading\Header::factory(['size' => 4, 'title' => 'Dump: ' . get_class($view)])->appendTailwinds(['ml-12']));

        $container = $page->addView(View::factory([])->appendTailwinds(['m-12']));
        $console = $container->addView(View\Console::factory());
        $console->addView(View::factory(['htmlTag' => 'code']))->setTextContent($view->getHtml($includeJs));
        $renderView = static::viewRenderer($page);
        $template = $renderView->getRenderTemplate();
        $template->tryDangerouslyAppendHtml('includeCss', static::service()->buildHtmlTag('link/', [
            'rel' => 'stylesheet',
            'type' => 'text/css',
            'href' => '/public/fohn-tailwind.css',
            'defer' => false,
        ]));
        $template->trySet('title', $page->title);

        return $template->renderToHtml();
    }

    protected function factoryTemplateFromFile(string $fileName): HtmlTemplate
    {
        $templateClass = $this->templateEngineClass;
        $template = new $templateClass();
        // check if exist and load.
        if (file_exists($fileName)) {
            $template->tryLoadFromFile($fileName);

            return $template;
        }
        // check within templateDirectories
        foreach ($this->templateDirectories as $dir) {
            if ($template->tryLoadFromFile($dir . \DIRECTORY_SEPARATOR . $fileName)) {
                return $template;
            }
        }

        throw (new Exception('Can not find template file'))
            ->addMoreInfo('filename', $fileName)
            ->addMoreInfo('template_dir', static::service()->templateDirectories);
    }

    public function renderExceptionAsHtml(\Throwable $exception): string
    {
        return (string) new Html($exception);
    }

    private function assertIsSubClassOfAbsctractView(string $className): void
    {
        $reflectionClass = new \ReflectionClass($className);

        if (!$reflectionClass->isSubclassOf(AbstractView::class)) {
            throw (new Exception('Trying to factory an instance of Class which is not a subClass of AbstractView.'))
                ->addMoreInfo('class', $className);
        }
    }
}
