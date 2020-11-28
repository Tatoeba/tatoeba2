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
        Cache::disable();
        $this->oldConfig = Configure::read();
        Configure::write('UI.languages', [
            'chi' => 'cmn',
            'cmn' => ['中文', 'Hans'],
            'eng' => ['English', null],
            'ita' => null,
            'jpn' => ['日本語', null],
        ]);
        $this->middleware = new LanguageSelectorMiddleware();
        $this->nextCallback = function ($req, $res) { return $res; };
    }

    function tearDown() {
        Cache::enable();
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
        $request = $this->createRequest('/jpn/some/path', 'jpn', 'GET');
        $response = ($this->middleware)($request, new Response(), $this->nextCallback);
        $this->assertEquals('ja', I18n::getLocale());
        $this->assertEquals('jpn', Configure::read('Config.language'));
    }

    public function simpleRedirectsProvider() {
        return [
            // URL, lang parameter, redirect-URL, status
            'root' => ['/', '', '/eng/', 301],
            'only action' => ['/about', '', '/eng/about', 301],
            'controller + action' => ['/sentences/index', '', '/eng/sentences/index', 301],
            'complete URL with parameters and query string' =>
            ['/sentences/show/123?foo=bar', '', '/eng/sentences/show/123?foo=bar', 301],
            'old language in URL' => ['/chi/index', 'chi', '/cmn/index', 302],
            'unmaintained language in URL' => ['/ita/index', 'ita', '/eng/index', 302],
            'no redirect' => ['/eng', 'eng', '', 200],
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
            'old cookie 1' =>
            ['/eng/index', ['CakeCookie' => '{"interfaceLanguage":"jpn"}'], '/jpn/index', 302],
            'old cookie 2' =>
            ['/eng/index', ['CakeCookie[interfaceLanguage]' => 'jpn'], '/jpn/index', 302],
            'new cookie' =>
            ['/eng/index', ['interface_language' => 'jpn'], '/jpn/index', 302],
            'no redirect' =>
            ['/eng/index', ['interface_language' => 'eng'], '', 200],
            'wrong language in cookie' =>
            ['/jpn/index', ['interface_language' => 'invalid'], '', 200],
            'unsupported language in cookie' =>
            ['/eng/index', ['interface_language' => 'deu'], '', 200],
            'old language in cookie' =>
            ['/eng/index', ['interface_language' => 'chi'], '/cmn/index', 302],
            'unmaintained language in cookie' =>
            ['/eng/index', ['interface_language' => 'ita'], '', 200],
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
            'simple header' => ['/eng/index', 'zh', '/cmn/index', 302],
            'more specific locale' => ['/eng/index', 'zh-Hans-CN', '/cmn/index', 302],
            'several locales, no redirect' =>
            ['/eng/index', 'en, fr, zh', null, 200],
            'several locales with redirect' =>
            ['/eng/index', 'fr, zh, en', '/cmn/index', 302],
            'with qualifiers, no redirect' => ['/eng/index', 'fr;q=0.5,zh;q=0.1,en;q=0.2', '', 200],
            'with qualifiers, redirect' => ['/eng/index', 'fr;q=0.5,zh;q=0.3,en;q=0.2', '/cmn/index', 302],
            'invalid header' => ['/jpn/index', 'invalid', '', 200],
            'unsupported languages in header' => ['/eng/index', 'de,fr,ru', '', 200],
            'unmaintained language in header' => ['/eng/index', 'it', '', 200],
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
        $request = $this->createRequest('/jpn/index', 'jpn', 'GET')
                        ->withHeader('Accept-Language', 'pt-BR')
                        ->withCookieParams(['interface_language' => 'cmn']);
        $this->assertResponse($request, '/cmn/index', 302);
    }

    public function testMiddleware_setsCookie() {
        foreach(['', 'jpn'] as $langPrefix) {
            $request = $this->createRequest("$langPrefix/index", 'jpn', 'GET');
            $response = ($this->middleware)($request, new Response(), $this->nextCallback);
            $this->assertEquals('jpn', $response->getCookie('interface_language')['value']);
        }
    }

    public function languageProvider () {
        return [
            ['', 'eng'],
            ['chi', 'cmn'],
            ['cmn', 'cmn'],
            ['ita', 'eng'],
            ['invalid', 'eng'],
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
            $this->assertEquals($expectedLang, Configure::read('Config.language'));
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
