<?php
App::import('Controller', 'Users');

class UsersControllerTest extends ControllerTestCase {
	public $fixtures = array(
		'app.sentence',
		'app.user',
		'app.group',
	);

	function setUp() {
		Configure::write('Security.salt', 'ze@9422#5dS?!99xx');
	}

	function tearDown() {
		$this->controller->Auth->Session->destroy();
	}

	function testCheckLogin_correctLoginAndPassword() {
		$this->testAction('/users/check_login', array(
			'data' => array('User' => array(
				'username' => 'contributor',
				'password' => '123456',
				'rememberMe' => 0,
			))
		));
		$this->assertTrue($this->controller->Auth->loggedIn());
	}

	function testCheckLogin_correctLoginAndincorrectPassword() {
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
}
