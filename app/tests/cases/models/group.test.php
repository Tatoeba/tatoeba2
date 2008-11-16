<?php 
/* SVN FILE: $Id$ */
/* Group Test cases generated on: 2008-11-12 01:11:43 : 1226448403*/
App::import('Model', 'Group');

class TestGroup extends Group {
	var $cacheSources = false;
	var $useDbConfig  = 'test_suite';
}

class GroupTestCase extends CakeTestCase {
	var $Group = null;
	var $fixtures = array('app.group', 'app.user', 'app.user');

	function start() {
		parent::start();
		$this->Group = new TestGroup();
	}

	function testGroupInstance() {
		$this->assertTrue(is_a($this->Group, 'Group'));
	}

	function testGroupFind() {
		$results = $this->Group->recursive = -1;
		$results = $this->Group->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('Group' => array(
			'id'  => 1,
			'name'  => 'Lorem ipsum dolor sit amet',
			'created'  => '2008-11-12 01:06:43',
			'modified'  => '2008-11-12 01:06:43'
			));
		$this->assertEqual($results, $expected);
	}
}
?>