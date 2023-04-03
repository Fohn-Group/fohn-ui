<?php

declare(strict_types=1);
/**
 * Performs test via Http request.
 */

namespace Fohn\Ui\Tests\TestCase;

use Fohn\Ui\App;
use Fohn\Ui\Service\Ui;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\FulfilledPromise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpTestCase extends \PHPUnit\Framework\TestCase
{
    private static array $_serverSuperglobalBackup;

    private ?App $app = null;

    public static function setUpBeforeClass(): void
    {
        self::$_serverSuperglobalBackup = $_SERVER;
    }

    public static function tearDownAfterClass(): void
    {
        $_SERVER = self::$_serverSuperglobalBackup;
    }

    protected function getApp(): App
    {
        if (!$this->app) {
            $this->app = new App(['registerShutdown' => false]);
        }

        return $this->app;
    }

    protected function setSuperglobalsFromRequest(RequestInterface $request): void
    {
        $_SERVER = [
            'REQUEST_METHOD' => $request->getMethod(),
            'HTTP_HOST' => $request->getUri()->getHost(),
            'REQUEST_URI' => (string) $request->getUri(),
            'QUERY_STRING' => $request->getUri()->getQuery(),
            'DOCUMENT_ROOT' => realpath(static::ROOT_DIR),
            'SCRIPT_FILENAME' => realpath(static::ROOT_DIR) . $request->getUri()->getPath(),
        ];

        $_GET = [];
        parse_str($request->getUri()->getQuery(), $queryArr);
        foreach ($queryArr as $k => $v) {
            $_GET[$k] = $v;
        }

        $_POST = [];
        parse_str($request->getBody()->getContents(), $queryArr);
        foreach ($queryArr as $k => $v) {
            $_POST[$k] = $v;
        }

        $_REQUEST = [];
        $_FILES = [];
        $_COOKIE = [];
        $_SESSION = [];
    }

    protected function getClient(string $contentType = 'text/html'): Client
    {
        $handler = function (RequestInterface $request) use ($contentType) {
            // emulate request
            $this->setSuperglobalsFromRequest($request);
            try {
                require static::ROOT_DIR . $request->getUri()->getPath();
                $response = $this->getApp()->getResponse(200, ['content-type', $contentType]);
                $response->getBody()->write(Ui::app()->getHtmlOutput());
            } catch (\Throwable $e) {
                $response = $this->getApp()->getResponse(500, ['content-type', 'text/html']);
                $response->getBody()->write('Error : ' . $e->getMessage());
            }
            // Rewind the body of the response if possible.
            $body = $response->getBody();
            if ($body->isSeekable()) {
                $body->rewind();
            }

            return new FulfilledPromise($response);
        };

        return new Client(['base_uri' => 'http://localhost/', 'handler' => $handler]);
    }

    protected function getResponseFromRequest(string $path, array $options = []): ResponseInterface
    {
        try {
            return $this->getClient()->request(isset($options['form_params']) !== null ? 'POST' : 'GET', $this->getPathWithAppVars($path), $options);
        } catch (\GuzzleHttp\Exception\ServerException $ex) {
            $exFactoryWithFullBody = new class('', $ex->getRequest()) extends \GuzzleHttp\Exception\RequestException {
                public static function getResponseBodySummary(ResponseInterface $response)
                {
                    return $response->getBody()->getContents();
                }
            };

            throw $exFactoryWithFullBody->create($ex->getRequest(), $ex->getResponse());
        }
    }

    protected function getPathWithAppVars(string $path): string
    {
        return 'app-test/' . $path;
    }
}
