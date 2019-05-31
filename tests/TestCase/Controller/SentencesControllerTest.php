<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

class SentencesControllerTest extends IntegrationTestCase {
    use TatoebaControllerTestTrait;

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
        'app.reindex_flags',
        'app.audios',
        'app.transcriptions',
        'app.contributions',
        'app.tags',
        'app.tags_sentences',
        'app.users_sentences',
    ];

    public function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
    }

    public function testAdd_redirectsGuestsToLogin() {
        $this->get('/jpn/sentences/add');
        $this->assertRedirect('/jpn/users/login?redirect=%2Fjpn%2Fsentences%2Fadd');
    }

    public function testAdd_doesNotRedirectsLoggedInUsers() {
        $this->logInAs('contributor');
        $this->get('/jpn/sentences/add');
        $this->assertNoRedirect();
        $this->assertResponseOk();
    }

    public function testEditSentence_doesntWorkForUnknownSentence() {
        $this->logInAs('contributor');
        $this->post('/jpn/sentences/edit_sentence', [
            'id' => 'epo_999999', 'value' => 'Forlasu!',
        ]);
        $this->assertRedirect('/jpn/home');
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

    public function testEditLicense_canEditAsUserWithPerm() {
        $sentenceId = 48;
        $sentences = TableRegistry::get('Sentences');
        $oldSentence = $sentences->get($sentenceId);
        $this->logInAs('contributor');
        $this->post('/jpn/sentences/edit_license', [
            'id' => $sentenceId,
            'license' => 'CC0 1.0',
        ]);
        $newSentence = $sentences->get($sentenceId);
        $this->assertNotEquals($oldSentence->license, $newSentence->license);
    }

    public function testEditLicense_cannotEditAsUserWithoutPerm() {
        $sentenceId = 54;
        $sentences = TableRegistry::get('Sentences');
        $oldSentence = $sentences->get($sentenceId);
        $this->logInAs('kazuki');
        $this->post('/jpn/sentences/edit_license', [
            'id' => $sentenceId,
            'license' => 'CC0 1.0',
        ]);
        $newSentence = $sentences->get($sentenceId);
        $this->assertEquals($oldSentence->license, $newSentence->license);
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

        $this->get("/eng/sentences/of_user/$user?page=9999999");

        $this->assertRedirect("/eng/sentences/of_user/$user?page=$lastPage");
    }
}
