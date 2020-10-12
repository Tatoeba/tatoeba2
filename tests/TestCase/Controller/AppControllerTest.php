<?php
namespace App\Test\TestCase\Controller;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;

class AppControllerTest extends IntegrationTestCase {
	use TatoebaControllerTestTrait;

	public $fixtures = array(
		'app.users',
		'app.users_languages',
		'app.private_messages',
	);

    function setUp() {
        parent::setUp();

        Cache::disable();
        Configure::write('UI.languages', [
            'chi' => 'cmn',
            'cmn' => ['中文', 'Hans'],
            'eng' => ['English', null],
            'jbo' => ['Lojban', null],
            'jpn' => ['日本語', null],
            'pt_BR' => ['Português (BR)', 'BR'],
        ]);
    }

	function tearDown() {
		parent::tearDown();
		Cache::enable();
	}

	function setInterfaceLanguageCookie($lang = null) {
		$this->cookie('CakeCookie', ['interfaceLanguage' => $lang]);
	}

	function assertInterfaceLanguageCookie($lang) {
		$this->assertCookie(json_encode(['interfaceLanguage' => $lang]), 'CakeCookie');
	}

	function testBeforeFilter_redirectsToEnglishByDefault() {
		$this->get('/about');
		$this->assertRedirect('/eng/about');
	}

	function testBeforeFilter_redirectsToEnglishByDefaultWithIndexAction() {
		$this->get('/wall/index');
		$this->assertRedirect('/eng/wall/index');
	}

	function testBeforeFilter_redirectsToLanguageInCookie() {
		$this->setInterfaceLanguageCookie('jpn');
		$this->get('/eng/about');
		$this->assertRedirect('/jpn/about');
	}

	function testBeforeFilter_redirectsToLanguageInCookieWithoutLanguageInUrl() {
		$this->setInterfaceLanguageCookie('jpn');
		$this->get('/about');
		$this->assertRedirect('/jpn/about');
	}

	function testBeforeFilter_doesntRedirectIfLanguageInCookieEqualsLanguageInUrl() {
		$this->setInterfaceLanguageCookie('eng');
		$this->get('/eng/about');
		$this->assertNoRedirect();
	}

	function testBeforeFilter_doesntRedirectIfEnglishWithoutCookie() {
		$this->get('/eng/about');
		$this->assertNoRedirect();
		$this->assertInterfaceLanguageCookie('eng');
	}

	function browserLanguagesProvider() {
		return [
			['/eng/', '/cmn/', 'zh'],
			['/eng/about', '/cmn/about', 'zh'],
			['/', '/cmn/', 'zh'],
			['/', '/cmn/', 'zh-CN'],
			['/', '/cmn/', 'zh-Hant-CN'],
			['/', '/cmn/', 'zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3'],
		];
	}

	/**
	 * @dataProvider browserLanguagesProvider
	 */
	function testBeforeFilter_honorsBrowserLanguage($landing, $redirect, $header) {
		$this->addHeader('Accept-Language', $header);
		$this->get($landing);
		$this->assertRedirect($redirect);
	}

	function testBeforeFilter_redirectsFromOldAliasWithLangInUrl() {
		$this->get('/chi/about');
		$this->assertRedirect('/cmn/about');
	}

	function testBeforeFilter_redirectsFromOldAliasWithCookie() {
		$this->setInterfaceLanguageCookie('chi');
		$this->get('/about');
		$this->assertRedirect('/chi/about');
	}

	function testBeforeFilter_redirectsFromOldAliasWithCookieWithLangInUrl() {
		$this->setInterfaceLanguageCookie('chi');
		$this->get('/chi/about');
		$this->assertRedirect('/cmn/about');
	}

	function testBeforeFilter_updatesCookieFromOldAlias() {
		$this->setInterfaceLanguageCookie('chi');
		$this->get('/cmn/about');
		$this->assertNoRedirect();
		$this->assertInterfaceLanguageCookie('cmn');
	}

	function setRememberMeCookie($username, $password) {
		$this->cookieEncrypted('User', compact('username', 'password'));
	}

	function testRememberMeAutomaticallyLogsInUser() {
		$this->setRememberMeCookie(
			'contributor',
			'0 $2a$10$Dn8/JT1xViULUEBCR5HiquLCXXB4/K3N2Nzc0PRZ.bfbmoApO55l6'
		);
		$this->get('/eng/about');

		$this->assertSession(4, 'Auth.User.id');
	}

	function testRememberMeFailsIfIncorrectPassword() {
		$this->setRememberMeCookie(
			'contributor',
			'0 $2a$10$Dn8/JT1xViULUEBCR5HiquLCXXB4/K3N2Nzc0PRZ.bfbmoApO55l4'
		);
		$this->get('/eng/about');

		$this->assertSession(null, 'Auth.User.id');
	}

	function testError404InProduction() {
		Configure::write('debug', false);
		$this->get('/eng/this_does_no_exists');
		$this->assertResponseCode(404);
	}

	function testLoginRedirectionDoesNotDisplayFlashMessage() {
		$this->get('/eng/sentences/add');
		$this->assertRedirect('/eng/users/login?redirect=%2Feng%2Fsentences%2Fadd');
		$this->assertNoFlashMessage();
	}
}
