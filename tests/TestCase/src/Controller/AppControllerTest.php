<?php
namespace App\Test\TestCase\Controller;

use App\Component\Cookie;
use App\Controller\App;
use Cake\Core\Configure;

class AppControllerTest extends ControllerTestCase {
	public $fixtures = array(
		'app.sentence',
		'app.user',
		'app.users_language',
	);

	function startTest($method) {
		Configure::write('UI.languages', array(
			array('cmn', 'Hans', '中文', array('chi')),
			array('eng', null, 'English'),
			array('jbo', null, 'Lojban'),
			array('jpn', null, '日本語'),
			array('pt_BR', 'BR', 'Português (BR)'),
		));
		Configure::write('App.base', ''); // prevent using the filesystem path as base
		$mockedComponents = array();
		if (strpos($method, "testBeforeFilter_") !== false) {
			$mockedComponents = array('Cookie' => array('read', 'write'));
		}
		$this->controller = $this->generate('App', array(
			'methods' => array('redirect', 'bar', 'index'),
			'components' => $mockedComponents,
		));
		$this->controller->Auth->allowedActions = array('bar', 'index');
	}

	function tearDown() {
		$this->controller->Auth->Session->destroy();
		unset($this->controller);
	}

	function setInterfaceLanguageCookie($lang = null) {
		$this->controller->Cookie
			->expects($this->any())
			->method('read')
			->will($this->returnCallback(
				function () use ($lang) {
					$args = func_get_args();
					if (isset($args[0]) && $args[0] == 'interfaceLanguage') {
						return $lang;
					}
				}
			));
	}

	function expectLanguageCookie($lang) {
		$this->controller->Cookie
			->expects($this->once())
			->method('write')
			->with('interfaceLanguage', $lang, false, '+1 month');
	}

	function expectNoLanguageCookie() {
		$this->controller->Cookie
			->expects($this->never())
			->method('write');
	}

	function testBeforeFilter_redirectsToEnglishByDefault() {
		$this->expectNoLanguageCookie();
		$this->controller
			->expects($this->once())
			->method('redirect')
			->with('/eng/foo/bar');

		$this->testAction('/foo/bar', array('method' => 'GET'));
	}

	function testBeforeFilter_redirectsToEnglishByDefaultWithIndexAction() {
		$this->expectNoLanguageCookie();
		$this->controller
			->expects($this->once())
			->method('redirect')
			->with('/eng/foo/index');

		$this->testAction('/foo/index', array('method' => 'GET'));
	}

	function testBeforeFilter_redirectsToLanguageInCookie() {
		$this->expectNoLanguageCookie();
		$this->setInterfaceLanguageCookie('jpn');
		$this->controller
			->expects($this->once())
			->method('redirect')
			->with('/jpn/foo/bar');

		$this->testAction('/eng/foo/bar', array('method' => 'GET'));
	}

	function testBeforeFilter_redirectsToLanguageInCookieWithoutLanguageInUrl() {
		$this->expectNoLanguageCookie();
		$this->setInterfaceLanguageCookie('jpn');
		$this->controller
			->expects($this->once())
			->method('redirect')
			->with('/jpn/foo/bar');

		$this->testAction('/foo/bar', array('method' => 'GET'));
	}

	function testBeforeFilter_doesntRedirectIfLanguageInCookieEqualsLanguageInUrl() {
		$this->expectNoLanguageCookie();
		$this->setInterfaceLanguageCookie('eng');
		$this->controller
			->expects($this->never())
			->method('redirect');

		$this->testAction('/eng/foo/bar', array('method' => 'GET'));
	}

	function testBeforeFilter_doesntRedirectIfEnglishWithoutCookie() {
		$this->expectLanguageCookie('eng');
		$this->controller
			->expects($this->never())
			->method('redirect');

		$this->testAction('/eng/foo/bar', array('method' => 'GET'));
	}

	function testBeforeFilter_redirectsFromOldAliasWithLangInUrl() {
		$this->expectNoLanguageCookie();
		$this->controller
			->expects($this->once())
			->method('redirect')
			->with('/cmn/foo/bar');

		$this->testAction('/chi/foo/bar', array('method' => 'GET'));
	}

	function testBeforeFilter_redirectsFromOldAliasWithCookie() {
		$this->expectNoLanguageCookie();
		$this->setInterfaceLanguageCookie('chi');
		$this->controller
			->expects($this->once())
			->method('redirect')
			->with('/chi/foo/bar');

		$this->testAction('/foo/bar', array('method' => 'GET'));
	}

	function testBeforeFilter_redirectsFromOldAliasWithCookieWithLangInUrl() {
		$this->expectNoLanguageCookie();
		$this->setInterfaceLanguageCookie('chi');
		$this->controller
			->expects($this->once())
			->method('redirect')
			->with('/cmn/foo/bar');

		$this->testAction('/chi/foo/bar', array('method' => 'GET'));
	}

	function testBeforeFilter_updatesCookieFromOldAlias() {
		$this->expectLanguageCookie('cmn');
		$this->setInterfaceLanguageCookie('chi');
		$this->controller
			->expects($this->never())
			->method('redirect');

		$this->testAction('/cmn/foo/bar', array('method' => 'GET'));
	}

	function testRememberMeAutomaticallyLogsInUser() {
		$this->controller->RememberMe->remember('contributor', '0 $2a$10$Dn8/JT1xViULUEBCR5HiquLCXXB4/K3N2Nzc0PRZ.bfbmoApO55l6');
		$this->assertFalse($this->controller->Auth->loggedIn());

		$this->testAction('/eng/foo/bar', array('method' => 'GET'));

		$this->assertTrue($this->controller->Auth->loggedIn());
	}

	function testRememberMeFailsIfIncorrectPassword() {
		$this->controller->RememberMe->remember('contributor', '0 $2a$10$Dn8/JT1xViULUEBCR5HiquLCXXB4/K3N2Nzc0PRZ.bfbmoApO55l4');

		$this->testAction('/eng/foo/bar', array('method' => 'GET'));

		$this->assertFalse($this->controller->Auth->loggedIn());
	}
}
