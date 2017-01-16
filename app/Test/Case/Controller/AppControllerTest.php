<?php
App::import('Controller', 'App');
App::import('Component', 'Cookie');

Mock::generate('CookieComponent');

class TestAppController extends AppController {
	public $uses = array();

	public $redirectUrl;
	public $stopped = false;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
		return parent::redirect($url, $status, $exit);
	}

	function header() {
		// Don't call header() for real
	}

	function _stop($status = 0) {
		$this->stopped = true;
	}
}

class AppControllerTest extends CakeTestCase {
	public $fixtures = array();

	function startTest($method) {
		Configure::write('UI.languages', array(
			array('cmn', 'Hans', '中文', array('chi')),
			array('eng', null, 'English'),
			array('jbo', null, 'Lojban'),
			array('jpn', null, '日本語'),
			array('pt_BR', 'BR', 'Português (BR)'),
		));
		$this->App =& new TestAppController();
		$this->App->constructClasses();
		$this->App->Cookie =& new MockCookieComponent();
	}

	function tearDown() {
		unset($this->App);
	}

	function setInterfaceLanguageCookie($lang = null) {
		$this->App->Cookie->setReturnValue('read', $lang, array('interfaceLanguage'));
	}

	function _runBeforeFilter($url) {
		$this->App->params = Dispatcher::parseParams($url);
		$parsedUrl = parse_url($url);
		if (isset($parsedUrl['query'])) {
			parse_str($parsedUrl['query'], $this->App->params['url']);
		}
		if (isset($parsedUrl['path'])) {
			$this->App->params['url']['url'] = $parsedUrl['path'];
		}
		$this->App->Component->initialize($this->App);
		$this->App->beforeFilter();
	}

	function expectLanguageCookie($lang) {
		$this->App->Cookie->expect('write', array('interfaceLanguage', $lang, false, '+1 month'));
		$this->App->Cookie->expectCallCount('write', 1);
	}

	function expectNoLanguageCookie() {
		$this->App->Cookie->expectNever('write');
	}

	function testBeforeFilter_redirectsToEnglishByDefault() {
		$this->expectNoLanguageCookie();
		$this->_runBeforeFilter('/foo/bar');

		$this->assertTrue($this->App->stopped);
		$this->assertEqual('/eng/foo/bar', $this->App->redirectUrl);
	}

	function testBeforeFilter_redirectsToLanguageInCookie() {
		$this->expectNoLanguageCookie();
		$this->setInterfaceLanguageCookie('jpn');

		$this->_runBeforeFilter('/eng/foo/bar');

		$this->assertTrue($this->App->stopped);
		$this->assertEqual('/jpn/foo/bar', $this->App->redirectUrl);
	}

	function testBeforeFilter_redirectsToLanguageInCookieWithoutLanguageInUrl() {
		$this->expectNoLanguageCookie();
		$this->setInterfaceLanguageCookie('jpn');

		$this->_runBeforeFilter('/foo/bar');

		$this->assertTrue($this->App->stopped);
		$this->assertEqual('/jpn/foo/bar', $this->App->redirectUrl);
	}

	function testBeforeFilter_doesntRedirectIfLanguageInCookieEqualsLanguageInUrl() {
		$this->expectNoLanguageCookie();
		$this->setInterfaceLanguageCookie('eng');

		$this->_runBeforeFilter('/eng/foo/bar');

		$this->assertFalse($this->App->stopped);
		$this->assertNull($this->App->redirectUrl);
	}

	function testBeforeFilter_doesntRedirectIfEnglishWithoutCookie() {
		$this->expectLanguageCookie('eng');
		$this->_runBeforeFilter('/eng/foo/bar');

		$this->assertFalse($this->App->stopped);
		$this->assertNull($this->App->redirectUrl);
	}

	function testBeforeFilter_redirectsFromOldAliasWithLangInUrl() {
		$this->expectNoLanguageCookie();
		$this->_runBeforeFilter('/chi/foo/bar');

		$this->assertTrue($this->App->stopped);
		$this->assertEqual('/cmn/foo/bar', $this->App->redirectUrl);
	}

	function testBeforeFilter_redirectsFromOldAliasWithCookie() {
		$this->expectNoLanguageCookie();
		$this->setInterfaceLanguageCookie('chi');

		$this->_runBeforeFilter('/foo/bar');

		$this->assertTrue($this->App->stopped);
		$this->assertEqual('/chi/foo/bar', $this->App->redirectUrl);
	}

	function testBeforeFilter_redirectsFromOldAliasWithCookieWithLangInUrl() {
		$this->expectNoLanguageCookie();
		$this->setInterfaceLanguageCookie('chi');

		$this->_runBeforeFilter('/chi/foo/bar');

		$this->assertTrue($this->App->stopped);
		$this->assertEqual('/cmn/foo/bar', $this->App->redirectUrl);
	}

	function testBeforeFilter_updatesCookieFromOldAlias() {
		$this->expectLanguageCookie('cmn');
		$this->setInterfaceLanguageCookie('chi');

		$this->_runBeforeFilter('/cmn/foo/bar');

		$this->assertFalse($this->App->stopped);
		$this->assertNull($this->App->redirectUrl);
	}
}
