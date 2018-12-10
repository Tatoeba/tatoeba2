<?php
namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

class SentencesControllerTest extends IntegrationTestCase {
	public $fixtures = [
		'app.sentences',
		'app.users',
		'app.users_languages',
		'app.sentences_sentences_lists',
		'app.languages',
		'app.links',
		'app.aros',
		'app.acos',
		'app.aros_acos',
		'app.private_messages',
	];

	public function setUp() {
		parent::setUp();
		Configure::write('Acl.database', 'test');
		Configure::write('Security.salt', 'ze@9422#5dS?!99xx');
	}

	private function logInAs($username) {
		$users = TableRegistry::get('Users');
		$user = $users->findByUsername($username)->first();
		$this->session(['Auth' => ['User' => $user->toArray()]]);
		$this->enableCsrfToken();
		$this->enableSecurityToken();
	}

	public function testAdd_redirectsGuestsToLogin() {
		$this->get('/jpn/sentences/add');
		$this->assertRedirect('/jpn/users/login?redirect=%2Fjpn%2Fsentences%2Fadd');
	}

	public function testAdd_doesNotRedirectsLoggedInUsers() {
		$this->logInAs('contributor');
		$this->get('/jpn/sentences/add');
		$this->assertNoRedirect();
	}

	public function testEditSentence_doesntWorkForUnknownSentence() {
		$this->logInAs('contributor');
		$this->post('/jpn/sentences/edit_sentence', [
			'id' => 'epo_999999', 'value' => 'Forlasu!',
		]);
		$this->assertRedirect('/jpn/home');
	}

	public function testEditSentence_canEditSentencesOfMyOwn() {
		$sentences = TableRegistry::get('Sentences');
		$oldSentence = $sentences->get(1);
		$this->logInAs('kazuki');
		$this->post('/jpn/sentences/edit_sentence', [
			'id' => 'eng_1', 'value' => 'Where are my…',
		]);
		$newSentence = $sentences->get(1);
		$this->assertNotEquals($oldSentence->text, $newSentence->text);
	}

	public function testEditSentence_cantEditSentencesOfOtherUsers() {
		$sentences = TableRegistry::get('Sentences');
		$oldSentence = $sentences->get(1);
		$this->logInAs('contributor');
		$this->post('/jpn/sentences/edit_sentence', [
			'id' => 'eng_1', 'value' => 'Where are my…',
		]);
		$newSentence = $sentences->get(1);
		$this->assertEquals($oldSentence->text, $newSentence->text);
	}

	public function testEditSentence_canEditSentencesOfOtherUsersIfModerator() {
		$sentences = TableRegistry::get('Sentences');
		$oldSentence = $sentences->get(1);
		$this->logInAs('corpus_maintainer');
		$this->post('/jpn/sentences/edit_sentence', [
			'id' => 'eng_1', 'value' => 'Where are my…',
		]);
		$newSentence = $sentences->get(1);
		$this->assertNotEquals($oldSentence->text, $newSentence->text);
	}

	public function testAdopt_cantAdoptSentenceIfNotOrphan() {
		$sentences = TableRegistry::get('Sentences');
		$oldSentence = $sentences->get(1);
		$this->logInAs('contributor');
		$this->get('/jpn/sentences/adopt/1');
		$newSentence = $sentences->get(1);
		$this->assertEquals($oldSentence->user_id, $newSentence->user_id);
	}

	public function testAdopt_cantLetGoSentenceIfNotOwner() {
		$sentences = TableRegistry::get('Sentences');
		$oldSentence = $sentences->get(1);
		$this->logInAs('contributor');
		$this->get('/jpn/sentences/let_go/1');
		$newSentence = $sentences->get(1);
		$this->assertEquals($oldSentence->user_id, $newSentence->user_id);
	}

	public function testDelete_cantDeleteOwnSentenceAsRegularUser() {
		$this->logInAs('kazuki');
		$this->get('/jpn/sentences/delete/1');
		$this->assertCount(1, $this->_controller->Sentences->findById(1));
	}

	public function testDelete_cantDeleteOthersSentenceAsRegularUser() {
		$this->logInAs('contributor');
		$this->get('/jpn/sentences/delete/1');
		$this->assertCount(1, $this->_controller->Sentences->findById(1));
	}

	public function testDelete_canDeleteSentenceAsCorpusMaintainer() {
		$this->logInAs('corpus_maintainer');
		$this->get('/jpn/sentences/delete/1');
		$this->assertCount(0, $this->_controller->Sentences->findById(1));
	}

	public function testDelete_canDeleteOwnSentenceIfLonely() {
		$lonelySentenceId = 7;
		$this->logInAs('kazuki');
		$this->get("/jpn/sentences/delete/$lonelySentenceId");
		$this->assertCount(0, $this->_controller->Sentences->findById($lonelySentenceId));
	}

	public function testDelete_cantDeleteOtherLonelySentences() {
		$lonelySentenceId = 7;
		$this->logInAs('contributor');
		$this->get("/jpn/sentences/delete/$lonelySentenceId");
		$this->assertCount(1, $this->_controller->Sentences->findById($lonelySentenceId));
	}

	public function testEditLicense_returnsHTTP400IfNoId() {
		$this->logInAs('contributor');
		$this->post('/jpn/sentences/edit_license', [
			'license' => 'CC0 1.0',
		]);
		$this->assertResponseCode(400);
	}

	public function testEditLicense_returnsHTTP400IfNoLicense() {
		$this->logInAs('contributor');
		$this->post('/jpn/sentences/edit_license', [
			'id' => 48,
		]);
		$this->assertResponseCode(400);
	}

	public function testEditLicense_cannotEditAsUser() {
		$sentenceId = 48;
		$sentences = TableRegistry::get('Sentences');
		$oldSentence = $sentences->get($sentenceId);
		$this->logInAs('contributor');
		$this->post('/jpn/sentences/edit_license', [
			'id' => $sentenceId,
			'license' => 'CC0 1.0',
		]);
		$newSentence = $sentences->get($sentenceId);
		$this->assertEquals($oldSentence->license, $newSentence->license);
	}

	public function testEditLicense_canEditIfCorpusMaintainer() {
		$sentenceId = 48;
		$sentences = TableRegistry::get('Sentences');
		$oldSentence = $sentences->get($sentenceId);
		$this->logInAs('corpus_maintainer');
		$this->post('/jpn/sentences/edit_license', [
			'id' => $sentenceId,
			'license' => 'CC0 1.0',
		]);
		$newSentence = $sentences->get($sentenceId);
		$this->assertNotEquals($oldSentence->license, $newSentence->license);
	}

	public function testEditLicense_canEditIfAdmin() {
		$sentenceId = 48;
		$sentences = TableRegistry::get('Sentences');
		$oldSentence = $sentences->get($sentenceId);
		$this->logInAs('admin');
		$this->post('/jpn/sentences/edit_license', [
			'id' => $sentenceId,
			'license' => 'CC0 1.0',
		]);
		$newSentence = $sentences->get($sentenceId);
		$this->assertNotEquals($oldSentence->license, $newSentence->license);
	}

	public function testEditLicense_bypassValidationIfCorpusMaintainer() {
		$sentenceId = 50;
		$sentences = TableRegistry::get('Sentences');
		$oldSentence = $sentences->get($sentenceId);
		$this->logInAs('corpus_maintainer');
		$this->post('/jpn/sentences/edit_license', [
			'id' => $sentenceId,
			'license' => 'CC0 1.0',
		]);
		$newSentence = $sentences->get($sentenceId);
		$this->assertNotEquals($oldSentence->license, $newSentence->license);
	}

	public function testEditLicense_bypassValidationIfAdmin() {
		$sentenceId = 50;
		$sentences = TableRegistry::get('Sentences');
		$oldSentence = $sentences->get($sentenceId);
		$this->logInAs('admin');
		$this->post('/jpn/sentences/edit_license', [
			'id' => $sentenceId,
			'license' => 'CC0 1.0',
		]);
		$newSentence = $sentences->get($sentenceId);
		$this->assertNotEquals($oldSentence->license, $newSentence->license);
	}

	public function testPaginateRedirectsPageOutOfBoundsToLastPage() {
		$user = 'kazuki';
		$userId = 7;
		$lastPage = 2;

		$newSentences = array();
		for ($i = 1; $i <= 100; $i++) {
			$newSentences[] = [
				'lang' => 'eng',
				'text' => "Ay ay ay $i.",
				'user_id' => $userId,
			];
			$newSentences[] = [
				'lang' => 'eng',
				'text' => "Oy oy oy $i.",
				'user_id' => 1,
			];
		}
		$sentences = TableRegistry::get('Sentences');
		$entities = $sentences->newEntities($newSentences);
		$result = $sentences->saveMany($entities);

		$this->get("/eng/sentences/of_user/$user/page:9999999");

		$this->assertRedirect("/eng/sentences/of_user/$user/page:$lastPage");
	}
}
