<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;

class SentenceAnnotationsControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.aros',
        'app.acos',
        'app.aros_acos',
        'app.private_messages',
        'app.sentence_annotations',
        'app.sentences',
        'app.users',
        'app.users_languages',
    ];

    public function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
    }

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/eng/sentence_annotations/index', null, '/eng/users/login?redirect=%2Feng%2Fsentence_annotations%2Findex' ],
            [ '/eng/sentence_annotations/index', 'contributor', true ],
            [ '/eng/sentence_annotations/show/6', null, '/eng/users/login?redirect=%2Feng%2Fsentence_annotations%2Fshow%2F6' ],
            [ '/eng/sentence_annotations/show/6', 'contributor', true ],
            [ '/eng/sentence_annotations/delete/1/6', null, '/eng/users/login?redirect=%2Feng%2Fsentence_annotations%2Fdelete%2F1%2F6' ],
            [ '/eng/sentence_annotations/delete/1/6', 'contributor', '/eng/sentence_annotations/show/6' ],
            [ '/eng/sentence_annotations/delete/9999999999/6', null, '/eng/users/login?redirect=%2Feng%2Fsentence_annotations%2Fdelete%2F9999999999%2F6' ],
            [ '/eng/sentence_annotations/search/ちょっと', null, '/eng/users/login?redirect=%2Feng%2Fsentence_annotations%2Fsearch%2F%E3%81%A1%E3%82%87%E3%81%A3%E3%81%A8' ],
            [ '/eng/sentence_annotations/search/ちょっと', 'contributor', true ],
            [ '/eng/sentence_annotations/delete/9999999999/6', null, '/eng/users/login?redirect=%2Feng%2Fsentence_annotations%2Fdelete%2F9999999999%2F6' ],
            [ '/eng/sentence_annotations/last_modified', null, true ],
            [ '/eng/sentence_annotations/last_modified', 'contributor', true ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public function testShowWithForm() {
        $this->logInAs('contributor');
        $this->post('/eng/sentence_annotations/show', ['sentence_id' => 6]);
        $this->assertRedirect('/eng/sentence_annotations/show/6');
    }

    private function saveSomething() {
        $this->post('/eng/sentence_annotations/save', [
            'id' => 3,
            'sentence_id' => 16,
            'meaning_id' => 1,
            'text' => 'blah blah blah',
        ]);
    }

    public function testSaveAsGuest() {
        $this->enableCsrfToken();
        $this->saveSomething();
        $this->assertRedirect('/eng/users/login');
    }

    public function testSave() {
        $this->logInAs('contributor');
        $this->saveSomething();
        $this->assertRedirect('/eng/sentence_annotations/show/16');
    }

    private function searchSomething() {
        $this->post('/eng/sentence_annotations/search', ['text' => 'ちょっと']);
    }

    public function testSearchAsGuest() {
        $this->enableCsrfToken();
        $this->searchSomething();
        $this->assertRedirect('/eng/users/login');
    }

    public function testSearch() {
        $this->logInAs('contributor');
        $this->searchSomething();
        $this->assertRedirect('/eng/sentence_annotations/search/%E3%81%A1%E3%82%87%E3%81%A3%E3%81%A8');
    }

    private function replaceSomething() {
        $this->post('/eng/sentence_annotations/replace', [
            'textToReplace' => 'ちょっと',
            'textReplacing' => 'ちょっと待って'
        ]);
    }

    public function testReplaceAsGuest() {
        $this->enableCsrfToken();
        $this->replaceSomething();
        $this->assertRedirect('/eng/users/login');
    }

    public function testReplace() {
        $this->logInAs('contributor');
        $this->replaceSomething();
        $this->assertResponseOk();
    }
}
