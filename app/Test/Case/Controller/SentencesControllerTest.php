<?php
/* Sentences Test cases generated on: 2014-04-17 01:15:39 : 1397690139*/
App::import('Controller', 'Sentences');
App::import('Component', 'Cookie');

Mock::generate('CookieComponent');

class TestSentencesController extends SentencesController {
	var $autoRender = false;

	var $redirectUrl;
	var $stopped = false;
	var $rendered;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
		return parent::redirect($url, $status, $exit);
	}

	function header() {
		// Don't call header() for real
	}

	function render($action = null, $layout = null, $file = null) {
		$this->rendered = $action;
	}

	function _stop($status = 0) {
		$this->stopped = true;
	}
}

class SentencesControllerTest extends CakeTestCase {
	var $fixtures = array(
		'app.sentence',
		'app.user',
		'app.group',
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
		'app.reindex_flag',
		'app.transcription',
		'app.users_language',
	);

	function setUp() {
		Configure::write('Acl.database', 'test_suite');
		$users = $this->_fixtures['app.user']->records;
		$this->users = Set::combine($users, '{n}.username', '{n}');
	}

	function startTest($method) {
		$this->Sentences =& new TestSentencesController();
		$this->Sentences->constructClasses();
		/* Replace the CookieComponent with a mock in order to prevent
		   the 'headers already sent' error when a cookie is written.
		*/
		$this->Sentences->Cookie =& new MockCookieComponent();
	}

	function endTest($method) {
		$this->Sentences->Session->destroy();
		unset($this->Sentences);
		ClassRegistry::flush();
	}

	function _testActionAsGuest($method, $params = array(), $args = array()) {
		return $this->_testActionAsUser($method, null, $params, $args);
	}

	function _testActionAsUser($method, $user = null, $params = array(), $args = array()) {
		if ($user) {
			$this->Sentences->Session->write('Auth.User', $this->users[$user]);
		}
		$this->Sentences->params = array_merge(array(
			'lang' => 'jpn',
			'controller' => 'sentences',
			'action' => $method,
			'pass' => $args,
			'named' => array(),
			'url' => array('url' => ''),
		), $params);
		$this->Sentences->Component->initialize($this->Sentences);
		$this->Sentences->beforeFilter();
		$this->Sentences->Component->startup($this->Sentences);
		if (!$this->Sentences->stopped) {
			call_user_func_array(array($this->Sentences, $method), $args);
		}
	}

	function testAdd_redirectsGuestsToLogin() {
		$this->_testActionAsGuest('add');
		$this->assertEqual('/users/login', $this->Sentences->redirectUrl);
	}

	function testAdd_doesNotRedirectsLoggedInUsers() {
		$this->_testActionAsUser('add', 'contributor');
		$this->assertNull($this->Sentences->redirectUrl);
	}

	function testEditSentence_doesntWorkForUnknownSentence() {
		$this->_testActionAsUser('edit_sentence', 'contributor', array(
			'form' => array('id' => 'epo_999999', 'value' => 'Forlasu!'),
		));
		$this->assertNotNull($this->Sentences->redirectUrl);
	}

	function testEditSentence_canEditSentencesOfMyOwn() {
		$oldSentence = $this->Sentences->Sentence->findById(1, 'text');
		$this->_testActionAsUser('edit_sentence', 'kazuki', array(
			'form' => array('id' => 'eng_1', 'value' => 'Where are my…'),
		));
		$newSentence = $this->Sentences->Sentence->findById(1, 'text');
		$this->assertNotEqual($oldSentence['Sentence']['text'], $newSentence['Sentence']['text']);
	}

	function testEditSentence_cantEditSentencesOfOtherUsers() {
		$oldSentence = $this->Sentences->Sentence->findById(1, 'text');
		$this->_testActionAsUser('edit_sentence', 'contributor', array(
			'form' => array('id' => 'eng_1', 'value' => 'Where are my…'),
		));
		$newSentence = $this->Sentences->Sentence->findById(1, 'text');
		$this->assertEqual($oldSentence['Sentence']['text'], $newSentence['Sentence']['text']);
	}

	function testEditSentence_canEditSentencesOfOtherUsersIfModerator() {
		$oldSentence = $this->Sentences->Sentence->findById(1, 'text');
		$this->_testActionAsUser('edit_sentence', 'corpus_maintainer', array(
			'form' => array('id' => 'eng_1', 'value' => 'Where are my…'),
		));
		$newSentence = $this->Sentences->Sentence->findById(1, 'text');
		$this->assertNotEqual($oldSentence['Sentence']['text'], $newSentence['Sentence']['text']);
	}

	function testAdopt_cantAdoptSentenceIfNotOrphan() {
		$oldSentence = $this->Sentences->Sentence->findById(1, 'user_id');
		$this->_testActionAsUser('adopt', 'contributor', array(), array(1));
		$newSentence = $this->Sentences->Sentence->findById(1, 'user_id');
		$this->assertEqual($oldSentence['Sentence']['user_id'], $newSentence['Sentence']['user_id']);
	}

	function testAdopt_cantLetGoSentenceIfNotOwner() {
		$oldSentence = $this->Sentences->Sentence->findById(1, 'user_id');
		$this->_testActionAsUser('let_go', 'contributor', array(), array(1));
		$newSentence = $this->Sentences->Sentence->findById(1, 'user_id');
		$this->assertEqual($oldSentence['Sentence']['user_id'], $newSentence['Sentence']['user_id']);
	}

	function testDelete_cantDeleteOwnSentenceAsRegularUser() {
		$this->_testActionAsUser('delete', 'kazuki', array(), array(1));
		$this->assertTrue($this->Sentences->Sentence->findById(1));
	}

	function testDelete_cantDeleteOthersSentenceAsRegularUser() {
		$this->_testActionAsUser('delete', 'contributor', array(), array(1));
		$this->assertTrue($this->Sentences->Sentence->findById(1));
	}

	function testDelete_canDeleteSentenceAsCorpusMaintainer() {
		$this->_testActionAsUser('delete', 'corpus_maintainer', array(), array(1));
		$this->assertFalse($this->Sentences->Sentence->findById(1));
	}

	function testDelete_canDeleteOwnSentenceIfLonely() {
		$lonelySentenceId = 7;
		$this->_testActionAsUser('delete', 'kazuki', array(), array($lonelySentenceId));
		$this->assertFalse($this->Sentences->Sentence->findById($lonelySentenceId));
	}

	function testDelete_cantDeleteOtherLonelySentences() {
		$lonelySentenceId = 7;
		$this->_testActionAsUser('delete', 'contributor', array(), array($lonelySentenceId));
		$this->assertTrue($this->Sentences->Sentence->findById($lonelySentenceId));
	}
}
