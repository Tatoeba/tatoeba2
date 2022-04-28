<?php
namespace App\Test\TestCase\Middleware;

use App\Middleware\LanguageSelectorMiddleware;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\I18n\I18n;
use Cake\TestSuite\TestCase;

class LanguageSelectorMiddlewareTest extends TestCase {

    private $oldConfig;
    private $oldLocale;
    private $middleware;
    private $nextCallback;

    function setUp() {
        parent::setUp();
        $this->oldLocale = I18n::getLocale();
        $this->oldConfig = Configure::read();
        Configure::write('UI.languages', [
            'chi' => 'zh-cn',
            'cmn' => 'zh-cn',
            'zh-cn' => ['中文', 'zh', 'zh-hans-cn'],
            'eng' => 'en',
            'en' => ['English'],
            'it' => null,
            'jpn' => 'ja',
            'ja' => ['日本語'],
        ]);
        $this->middleware = new LanguageSelectorMiddleware();
        $this->nextCallback = function ($req, $res) { return $res; };
    }

    function tearDown() {
        Configure::write($this->oldConfig);
        I18n::setLocale($this->oldLocale);
        parent::tearDown();
    }

    private function assertResponse($request, $redirectUrl, $status) {
        $response = ($this->middleware)($request, new Response(), $this->nextCallback);
        $this->assertEquals($redirectUrl, $response->getHeaderLine('location'));
        $this->assertEquals($status, $response->getStatusCode());
    }

    private function createRequest($url, $lang, $method) {
        return (new ServerRequest(compact('url')))
            ->withMethod($method)
            ->withParam('lang', $lang);
    }

    public function testMiddleware_setsLocaleAndConfig() {
        $request = $this->createRequest('/ja/some/path', 'ja', 'GET');
        $response = ($this->middleware)($request, new Response(), $this->nextCallback);
        $this->assertEquals('ja', I18n::getLocale());
    }

    public function simpleRedirectsProvider() {
        return [
            // URL, lang parameter, redirect-URL, status
            'root' => ['/', '', '/en/', 301],
            'only action' => ['/about', '', '/en/about', 301],
            'controller + action' => ['/sentences/index', '', '/en/sentences/index', 301],
            'complete URL with parameters and query string' =>
            ['/sentences/show/123?foo=bar', '', '/en/sentences/show/123?foo=bar', 301],
            'old iso-2 language in URL' => ['/chi/index', 'chi', '/zh-cn/index', 302],
            'old iso-3 language in URL' => ['/cmn/index', 'cmn', '/zh-cn/index', 302],
            'unmaintained language in URL' => ['/it/index', 'it', '/en/index', 302],
            'no redirect' => ['/en', 'en', '', 200],
        ];
    }

    /**
     * @dataProvider simpleRedirectsProvider
     */
    public function testMiddleware_simpleRedirects($url, $lang, $redirectUrl, $status) {
        $request = $this->createRequest($url, $lang, 'GET');
        $this->assertResponse($request, $redirectUrl, $status);
    }

    public function withCookieProvider() {
        return [
            // URL, cookie, redirect-URL, status
            'new cookie' =>
            ['/en/index', ['interface_language' => 'ja'], '/ja/index', 302],
            'no redirect' =>
            ['/en/index', ['interface_language' => 'en'], '', 200],
            'wrong language in cookie' =>
            ['/ja/index', ['interface_language' => 'invalid'], '', 200],
            'unsupported language in cookie' =>
            ['/en/index', ['interface_language' => 'de'], '', 200],
            'old iso-2 language in cookie' =>
            ['/en/index', ['interface_language' => 'chi'], '/zh-cn/index', 302],
            'old iso-3 language in cookie' =>
            ['/en/index', ['interface_language' => 'jpn'], '/ja/index', 302],
            'unmaintained language in cookie' =>
            ['/en/index', ['interface_language' => 'it'], '', 200],
        ];
    }

    /**
     * @dataProvider withCookieProvider
     */
    public function testMiddleware_withCookie($url, $cookie, $redirectUrl, $status) {
        $request = $this->createRequest($url, explode('/', $url, 3)[1], 'GET')
                        ->withCookieParams($cookie);
        $this->assertResponse($request, $redirectUrl, $status);
    }

    public function withAcceptLanguageProvider() {
        return [
            // URL, header, redirect-URL, status
            'simple header' => ['/en/index', 'ja', '/ja/index', 302],
            'more specific locale' => ['/en/index', 'zh-Hans-CN', '/zh-cn/index', 302],
            'several locales, no redirect' =>
            ['/en/index', 'en, fr, zh', null, 200],
            'several locales with redirect' =>
            ['/en/index', 'fr, zh, en', '/zh-cn/index', 302],
            'with qualifiers, no redirect' => ['/en/index', 'fr;q=0.5,zh;q=0.1,en;q=0.2', '', 200],
            'with qualifiers, redirect' => ['/en/index', 'fr;q=0.5,zh;q=0.3,en;q=0.2', '/zh-cn/index', 302],
            'invalid header' => ['/ja/index', 'invalid', '', 200],
            'unsupported languages in header' => ['/en/index', 'de,fr,ru', '', 200],
            'unmaintained language in header' => ['/en/index', 'it', '', 200],
        ];
    }

    /**
     * @dataProvider withAcceptLanguageProvider
     */
    public function testMiddleware_withAcceptLanguageHeader($url, $header, $redirectUrl, $status) {
        $request = $this->createRequest($url, explode('/', $url, 3)[1], 'GET')
                        ->withHeader('Accept-Language', $header);
        $this->assertResponse($request, $redirectUrl, $status);
    }

    public function testMiddleware_cookieBeatsAll() {
        $request = $this->createRequest('/ja/index', 'ja', 'GET')
                        ->withHeader('Accept-Language', 'pt-BR')
                        ->withCookieParams(['interface_language' => 'zh-cn']);
        $this->assertResponse($request, '/zh-cn/index', 302);
    }

    public function testMiddleware_setsCookie() {
        foreach(['', 'ja'] as $langPrefix) {
            $request = $this->createRequest("$langPrefix/index", 'ja', 'GET');
            $response = ($this->middleware)($request, new Response(), $this->nextCallback);
            $this->assertEquals('ja', $response->getCookie('interface_language')['value']);
        }
    }

    public function languageProvider () {
        return [
            ['', 'en'],
            ['chi', 'zh-cn'],
            ['cmn', 'zh-cn'],
            ['it', 'en'],
            ['invalid', 'en'],
        ];
    }

    /**
     * @dataProvider languageProvider
     */
    public function testMiddleware_specialRequests($lang, $expectedLang) {
        foreach(['POST', 'PUT', 'AJAX'] as $method) {
            if ($method === 'AJAX') {
                $request = $this->createRequest("$lang/some/path", $lang, 'GET')
                                ->withHeader('Accept', 'application/json')
                                ->withHeader('X-Requested-With', 'XMLHttpRequest');
            } else {
                $request = $this->createRequest("$lang/some/path", $lang, $method);
            }
            $response = ($this->middleware)($request, new Response(), $this->nextCallback);
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertEquals($expectedLang, I18n::getLocale());
        }
    }

    public function testMiddleware_ignorePluginPaths() {
        $request = $this->createRequest('/some/plugin', '', 'GET')
                        ->withParam('plugin', 'Plugin');
        $response = ($this->middleware)($request, new Response(), $this->nextCallback);
        $this->assertEquals($this->oldLocale, I18n::getLocale());
        $this->assertEquals(200, $response->getStatusCode());
    }
}
