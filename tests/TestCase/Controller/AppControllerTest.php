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
