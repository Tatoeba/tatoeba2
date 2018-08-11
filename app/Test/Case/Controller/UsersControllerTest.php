<?php
App::import('Controller', 'Users');

class UsersControllerTest extends ControllerTestCase {
	public $fixtures = array(
		'app.aro',
		'app.aco',
		'app.aros_aco',
		'app.sentence',
		'app.user',
		'app.group',
		'app.users_language',
	);

	function setUp() {
		Configure::write('Acl.database', 'test');
		Configure::write('Security.salt', 'ze@9422#5dS?!99xx');
	}

	function tearDown() {
		$this->controller->Auth->Session->destroy();
	}

	function testCheckLogin_correctLoginAndPasswordV0() {
		$this->testAction('/users/check_login', array(
			'data' => array('User' => array(
				'username' => 'contributor',
				'password' => '123456',
				'rememberMe' => 0,
			))
		));
		$this->assertTrue($this->controller->Auth->loggedIn());
	}

	function testCheckLogin_correctLoginAndincorrectPasswordV0() {
		$this->testAction('/users/check_login', array(
			'data' => array('User' => array(
				'username' => 'contributor',
				'password' => 'this_is_incorrect',
				'rememberMe' => 0,
			))
		));
		$this->assertFalse($this->controller->Auth->loggedIn());
	}

	function testCheckLogin_incorrectLoginAndPassword() {
		$this->testAction('/users/check_login', array(
			'data' => array('User' => array(
				'username' => 'this_user_does_not_exist',
				'password' => 'this_is_incorrect',
				'rememberMe' => 0,
			))
		));
		$this->assertFalse($this->controller->Auth->loggedIn());
	}

	function testCheckLogin_correctLoginAndPassowrdV1() {
		$this->testAction('/users/check_login', array(
			'data' => array('User' => array(
				'username' => 'kazuki',
				'password' => 'myAwesomePassword',
				'rememberMe' => 0,
			))
		));
		$this->assertTrue($this->controller->Auth->loggedIn());
	}

	function testCheckLogin_correctLoginAndIncorrectPassowrdV1() {
		$this->testAction('/users/check_login', array(
			'data' => array('User' => array(
				'username' => 'kazuki',
				'password' => 'this_is_incorrect',
				'rememberMe' => 0,
			))
		));
		$this->assertFalse($this->controller->Auth->loggedIn());
	}

	function testCheckLogin_userWithOldStylePasswordCannotLogin() {
		$this->testAction('/users/check_login', array(
			'data' => array('User' => array(
				'username' => 'mr_old_style_passwd',
				'password' => '123456',
				'rememberMe' => 0,
			))
		));
		$this->assertFalse($this->controller->Auth->loggedIn());
	}

	function testCheckLogin_canRegister() {
		$this->testAction('/users/register', array(
			'data' => array('User' => array(
				'username' => 'polochon',
				'password' => 'very bad password',
				'language' => 'none',
				'acceptation_terms_of_use' => '1',
				'email' => 'polochon@example.net',
				'quiz' => 'poloc',
			))
		));
		$this->assertTrue($this->controller->Auth->loggedIn());
	}

	function testCheckLogin_cannotRegisterWithEmptyPassword() {
		$this->testAction('/users/register', array(
			'data' => array('User' => array(
				'username' => 'polochon',
				'password' => '',
				'language' => 'none',
				'acceptation_terms_of_use' => '1',
				'email' => 'polochon@example.net',
				'quiz' => 'poloc',
			))
		));
		$this->assertFalse($this->controller->Auth->loggedIn());
	}

	function testCheckLogin_loginUpdatedPasswordVersion() {
		$this->testAction('/users/check_login', array(
			'data' => array('User' => array(
				'username' => 'contributor',
				'password' => '123456',
				'rememberMe' => 0,
			))
		));

		$user = $this->controller->User->findByUsername('contributor');
		list($version, $hash) = explode(' ', $user['User']['password'], 2);
		$this->assertEquals(1, $version);
	}
}
