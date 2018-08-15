<?php
App::uses('SentencesController', 'Controller');

class SentencesControllerTest extends ControllerTestCase {
	public $fixtures = array(
		'app.sentence',
		'app.user',
		'app.users_language',
		'app.contribution',
		'app.sentences_sentences_list',
		'app.tag',
		'app.tags_sentence',
		'app.language',
		'app.link',
		'app.aro',
		'app.aco',
		'app.aros_aco',
		'app.reindex_flag',
		'app.transcription',
		'app.audio',
		'app.users_sentence',
	);

	public function setUp() {
		$_COOKIE = array();
		Configure::write('Acl.database', 'test');
		$this->controller = $this->generate('Sentences', array(
			'methods' => array('redirect'),
		));
	}

	public function endTest($method) {
		$this->controller->Auth->Session->destroy();
		unset($this->controller);
	}

	private function logInAs($username) {
		$user = $this->controller->Sentence->User->find('first', array(
			'conditions' => array('username' => $username),
		));
		$this->controller->Auth->login($user['User']);
	}

	public function testAdd_redirectsGuestsToLogin() {
		$this->controller
			->expects($this->once())
			->method('redirect')
			->with(array(
				'controller' => 'users',
				'action' => 'login',
				'plugin' => null,
			));

		$this->testAction('/jpn/sentences/add', array(
			'method' => 'get',
		));
	}

	public function testAdd_doesNotRedirectsLoggedInUsers() {
		$this->controller
			->expects($this->never())
			->method('redirect');
		$this->logInAs('contributor');

		$this->testAction('/jpn/sentences/add');
	}

	public function testEditSentence_doesntWorkForUnknownSentence() {
		$this->controller
			->expects($this->once())
			->method('redirect')
			->with(array(
				'controller' => 'pages',
				'action' => 'home',
			));
		$this->logInAs('contributor');

		$this->testAction('/jpn/sentences/edit_sentence', array(
			'data' => array('id' => 'epo_999999', 'value' => 'Forlasu!'),
		));
	}

	public function testEditSentence_canEditSentencesOfMyOwn() {
		$oldSentence = $this->controller->Sentence->findById(1, 'text');
		$this->logInAs('kazuki');
		$this->testAction('/jpn/sentences/edit_sentence', array(
			'data' => array('id' => 'eng_1', 'value' => 'Where are my…'),
		));
		$newSentence = $this->controller->Sentence->findById(1, 'text');
		$this->assertNotEquals($oldSentence['Sentence']['text'], $newSentence['Sentence']['text']);
	}

	public function testEditSentence_cantEditSentencesOfOtherUsers() {
		$oldSentence = $this->controller->Sentence->findById(1, 'text');
		$this->logInAs('contributor');
		$this->testAction('/jpn/sentences/edit_sentence', array(
			'data' => array('id' => 'eng_1', 'value' => 'Where are my…'),
		));
		$newSentence = $this->controller->Sentence->findById(1, 'text');
		$this->assertEquals($oldSentence['Sentence']['text'], $newSentence['Sentence']['text']);
	}

	public function testEditSentence_canEditSentencesOfOtherUsersIfModerator() {
		$oldSentence = $this->controller->Sentence->findById(1, 'text');
		$this->logInAs('corpus_maintainer');
		$this->testAction('/jpn/sentences/edit_sentence', array(
			'data' => array('id' => 'eng_1', 'value' => 'Where are my…'),
		));
		$newSentence = $this->controller->Sentence->findById(1, 'text');
		$this->assertNotEquals($oldSentence['Sentence']['text'], $newSentence['Sentence']['text']);
	}

	public function testAdopt_cantAdoptSentenceIfNotOrphan() {
		$oldSentence = $this->controller->Sentence->findById(1, 'user_id');
		$this->logInAs('contributor');
		$this->testAction('/jpn/sentences/adopt/1');
		$newSentence = $this->controller->Sentence->findById(1, 'user_id');
		$this->assertEquals($oldSentence['Sentence']['user_id'], $newSentence['Sentence']['user_id']);
	}

	public function testAdopt_cantLetGoSentenceIfNotOwner() {
		$oldSentence = $this->controller->Sentence->findById(1, 'user_id');
		$this->logInAs('contributor');
		$this->testAction('/jpn/sentences/let_go/1');
		$newSentence = $this->controller->Sentence->findById(1, 'user_id');
		$this->assertEquals($oldSentence['Sentence']['user_id'], $newSentence['Sentence']['user_id']);
	}

	public function testDelete_cantDeleteOwnSentenceAsRegularUser() {
		$this->logInAs('kazuki');
		$this->testAction('/jpn/sentences/delete/1');
		$this->assertCount(1, $this->controller->Sentence->findById(1));
	}

	public function testDelete_cantDeleteOthersSentenceAsRegularUser() {
		$this->logInAs('contributor');
		$this->testAction('/jpn/sentences/delete/1');
		$this->assertCount(1, $this->controller->Sentence->findById(1));
	}

	public function testDelete_canDeleteSentenceAsCorpusMaintainer() {
		$this->logInAs('corpus_maintainer');
		$this->testAction('/jpn/sentences/delete/1');
		$this->assertCount(0, $this->controller->Sentence->findById(1));
	}

	public function testDelete_canDeleteOwnSentenceIfLonely() {
		$lonelySentenceId = 7;
		$this->logInAs('kazuki');
		$this->testAction("/jpn/sentences/delete/$lonelySentenceId");
		$this->assertCount(0, $this->controller->Sentence->findById($lonelySentenceId));
	}

	public function testDelete_cantDeleteOtherLonelySentences() {
		$lonelySentenceId = 7;
		$this->logInAs('contributor');
		$this->testAction("/jpn/sentences/delete/$lonelySentenceId");
		$this->assertCount(1, $this->controller->Sentence->findById($lonelySentenceId));
	}
}
