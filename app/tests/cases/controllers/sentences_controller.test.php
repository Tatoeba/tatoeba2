<?php
/* Sentences Test cases generated on: 2014-04-17 01:15:39 : 1397690139*/
App::import('Controller', 'Sentences');
App::import('Component', 'Cookie');

Mock::generate('CookieComponent');

class TestSentencesController extends SentencesController {
	var $autoRender = false;

	var $redirectUrl;
	var $stopped;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}

	function _stop($status = 0) {
		$this->stopped = $status;
	}
}

class SentencesControllerTestCase extends CakeTestCase {
	var $fixtures = array(
		'app.sentence',
		'app.user',
		'app.group',
		'app.country',
		'app.sentence_comment',
		'app.contribution',
		'app.sentences_list',
		'app.sentences_sentences_list',
		'app.wall',
		'app.wall_thread',
		'app.favorites_user',
		'app.tag',
		'app.tags_sentence',
		'app.language',
		'app.link',
		'app.sentence_annotation',
		'app.aro',
		'app.aco',
		'app.aros_aco',
	);

	function setUp() {
		Configure::write('Acl.database', 'test_suite');
	}

	function startTest() {
		$this->Sentences =& new TestSentencesController();
		$this->Sentences->constructClasses();
		/* Replace the CookieComponent with a mock in order to prevent
		   the 'headers already sent' error when a cookie is written.
		*/
		$this->Sentences->Cookie =& new MockCookieComponent();
	}

	function endTest() {
		$this->Sentences->Session->destroy();
		unset($this->Sentences);
		ClassRegistry::flush();
	}

	function testAdd_redirectsGuestsToLogin() {
		$this->Sentences->params = array(
			'lang' => 'jpn',
			'controller' => 'sentences',
			'action' => 'add',
		);
		$this->Sentences->beforeFilter();
		$this->Sentences->Component->initialize($this->Sentences);
		$this->Sentences->Component->startup($this->Sentences);
		$this->Sentences->add();

		$this->assertEqual('/users/login', $this->Sentences->redirectUrl);
	}

	function testAdd_doesNotRedirectsLoggedInUsers() {
		$this->Sentences->Session->write('Auth.User', array(
			'id' => 4,
			'username' => 'contributor',
		));
		$this->Sentences->params = array(
			'lang' => 'jpn',
			'controller' => 'sentences',
			'action' => 'add',
		);
		$this->Sentences->beforeFilter();
		$this->Sentences->Component->initialize($this->Sentences);
		$this->Sentences->Component->startup($this->Sentences);
		$this->Sentences->add();

		$this->assertNull($this->Sentences->redirectUrl);
	}
}
