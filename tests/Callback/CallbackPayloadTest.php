<?php

declare(strict_types=1);
/**
 * Perform http request in order to
 * execute Callback using payload from request.
 */

namespace Fohn\Ui\Tests\Callback;

use Fohn\Ui\Callback\Request;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class CallbackPayloadTest extends TestCase
{
    protected Client $client;

    protected function setUp(): void
    {
        require_once __DIR__ . '/init-configuration.php';
        $config = loadConfig();

        $this->client = new Client(['base_uri' => $config['base_uri']]);
    }

    public function testDataCallback(): void
    {
        $payload = ['p1' => 'v1'];
        $expected = [
            'success' => true,
            'count' => 2,
            'results' => array_merge($payload, ['d1' => 'test1']),
        ];

        $request = new \GuzzleHttp\Psr7\Request('POST', '/tests/Callback/callback.php');
        // Send Request
        $response = $this->client->send($request, [
            'query' => [
                Request::URL_QUERY_TARGET => 'data_tg',
                'data_tg' => Request::DATA_TYPE,
            ],
            'body' => json_encode($payload),
        ]);
        // Read Response
        $responseBody = (string) $response->getBody();
        $this->assertSame(200, $response->getStatusCode(), ' Status error on Data callback.');
        $this->assertSame(json_decode($responseBody, true), $expected);
    }

    public function testAjaxCallback(): void
    {
        $payload = ['p1' => 'v1'];
        $expected = [
            'success' => true,
            'jsRendered' => 'console.log(v1)',
        ];

        $request = new \GuzzleHttp\Psr7\Request('POST', '/tests/Callback/callback.php');
        // Send Request
        $response = $this->client->send($request, [
            'query' => [
                Request::URL_QUERY_TARGET => 'ajax_tg',
                'ajax_tg' => Request::AJAX_TYPE,
            ],
            'body' => json_encode($payload),
        ]);
        // Read Response
        $responseBody = (string) $response->getBody();
        $this->assertSame(200, $response->getStatusCode(), ' Status error on Ajax callback.');
        $this->assertSame(json_decode($responseBody, true), $expected);
    }

    public function testJQueryCallback(): void
    {
        $payload = ['p1' => 'v1'];
        $expected = [
            'success' => true,
            'jsRendered' => 'console.log(v1)',
        ];

        $request = new \GuzzleHttp\Psr7\Request('POST', '/tests/Callback/callback.php');
        // Send Request
        $response = $this->client->send($request, [
            'query' => [
                Request::URL_QUERY_TARGET => 'jquery_tg',
                'jquery_tg' => Request::JQUERY_TYPE,
            ],
            'body' => json_encode($payload),
        ]);
        // Read Response
        $responseBody = (string) $response->getBody();
        $this->assertSame(200, $response->getStatusCode(), ' Status error on JQuery callback.');
        $this->assertSame(json_decode($responseBody, true), $expected);
    }

    public function testJQueryReloadCallback(): void
    {
        $payload = ['p1' => 'v1'];
        $expected = [
            'success' => true,
            'message' => 'Success',
            'jsRendered' => '',
            'html' => '<div id="v-test" class="" style="" data-ui-name="view">v1</div>',
            'id' => 'v-test',
        ];

        $request = new \GuzzleHttp\Psr7\Request('POST', '/tests/Callback/callback.php');
        // Send Request
        $response = $this->client->send($request, [
            'query' => [
                Request::URL_QUERY_TARGET => 'jq_reload_tg',
                'jq_reload_tg' => Request::JQUERY_TYPE,
            ],
            'body' => json_encode($payload),
        ]);
        // Read Response
        $responseBody = (string) $response->getBody();
        $this->assertSame(200, $response->getStatusCode(), ' Status error on JQueryReload callback.');
        $this->assertSame(json_decode($responseBody, true), $expected);
    }

    public function testGenericCallback(): void
    {
        $payload = ['p1' => 'v1'];
        $request = new \GuzzleHttp\Psr7\Request('POST', '/tests/Callback/callback.php');
        // Send Request
        $response = $this->client->send($request, [
            'query' => [
                Request::URL_QUERY_TARGET => 'generic_tg',
                'generic_tg' => Request::GENERIC_TYPE,
            ],
            'body' => json_encode($payload),
        ]);
        // Read Response
        $responseBody = (string) $response->getBody();
        $this->assertSame(200, $response->getStatusCode(), ' Status error on Generic callback.');
        $this->assertSame('', $responseBody);
    }

    public function executeGenericCallback(array $payload)
    {
        $this->assertSame(['p1' => 'v1'], $payload);
    }
}
