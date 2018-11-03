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
		'app.last_contribution',
	);

	function setUp() {
		Configure::write('App.base', ''); // prevent using the filesystem path as base
		Configure::write('Acl.database', 'test');
		Configure::write('Security.salt', 'ze@9422#5dS?!99xx');
		$this->controller = $this->generate('Users', array(
			'methods' => array('redirect'),
		));
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

	public function testPaginateRedirectsPageOutOfBoundsToLastPage() {
		$lastPage = 2;
		$users = array();
		for ($i = 1; $i <= 20; $i++) {
			$users[] = array(
				'username' => "foobar_$i",
				'email' => "foobar_$i@example.com",
			);
		}
		$this->controller->User->saveMany($users);

		$this->controller
			 ->expects($this->once())
			 ->method('redirect')
			 ->with("/eng/users/all/page:$lastPage/sort:User.group_id/direction:asc");
		$this->testAction('/eng/users/all/page:9999999/sort:User.group_id/direction:asc');
	}

}
