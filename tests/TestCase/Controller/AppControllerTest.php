<?php
namespace App\Test\TestCase\Controller;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;
use Cake\Utility\Security;

class AppControllerTest extends IntegrationTestCase {
	use TatoebaControllerTestTrait;

	public $fixtures = array(
		'app.Users',
		'app.UsersLanguages',
		'app.PrivateMessages',
		'app.WikiArticles',
	);

	function setRememberMeCookie($username, $password, $legacy = false) {
		$this->cookieEncrypted(
			'User',
			compact('username', 'password'),
			'aes',
			$legacy ? Security::getSalt() : Configure::read('Security.cookieKey')
		);
	}

	function testRememberMeAutomaticallyLogsInUser() {
		$this->setRememberMeCookie(
			'contributor',
			'0 $2a$10$Dn8/JT1xViULUEBCR5HiquLCXXB4/K3N2Nzc0PRZ.bfbmoApO55l6'
		);
		$this->get('/en/about');

		$this->assertSession(4, 'Auth.User.id');
	}

	function testRememberMeAutomaticallyLogsInUserWithLegacyCookie() {
		$this->setRememberMeCookie(
			'contributor',
			'0 $2a$10$Dn8/JT1xViULUEBCR5HiquLCXXB4/K3N2Nzc0PRZ.bfbmoApO55l6',
			true
		);
		$this->get('/en/about');

		$this->assertSession(4, 'Auth.User.id');
	}

	function testRememberMeFailsIfIncorrectPassword() {
		$this->setRememberMeCookie(
			'contributor',
			'0 $2a$10$Dn8/JT1xViULUEBCR5HiquLCXXB4/K3N2Nzc0PRZ.bfbmoApO55l4'
		);
		$this->get('/en/about');

		$this->assertSession(null, 'Auth.User.id');
	}

	function testError404InProduction() {
		Configure::write('debug', false);
		$this->get('/en/this_does_no_exists');
		$this->assertResponseCode(404);
	}

	function testLoginRedirectionDoesNotDisplayFlashMessage() {
		$this->get('/en/sentences/add');
		$this->assertRedirect('/en/users/login?redirect=%2Fen%2Fsentences%2Fadd');
		$this->assertNoFlashMessage();
	}
}
