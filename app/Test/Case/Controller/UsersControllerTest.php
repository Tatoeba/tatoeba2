<?php
/* SVN FILE: $Id$ */
/* UsersController Test cases generated on: 2008-11-12 01:11:55 : 1226448115*/
App::import('Controller', 'Users');

class TestUsers extends UsersController {
	public $autoRender = false;
}

class UsersControllerTest extends CakeTestCase {
	public $Users = null;

	function setUp() {
		$this->Users = new TestUsers();
	}

	function testUsersControllerInstance() {
		$this->assertTrue(is_a($this->Users, 'UsersController'));
	}

	function tearDown() {
		unset($this->Users);
	}
}
?>