<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestCase;

class SentenceCommentsControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.private_messages',
        'app.sentence_comments',
        'app.sentences',
        'app.transcriptions',
        'app.users',
        'app.users_languages',
        'app.wiki_articles',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/eng/sentence_comments/index', null, true ],
            [ '/eng/sentence_comments/index', 'contributor', true ],
            [ '/eng/sentence_comments/index/fra', null, true ],
            [ '/eng/sentence_comments/index/fra', 'contributor', true ],
            [ '/eng/sentence_comments/show/4', null, '/eng/sentences/show/4' ],
            [ '/eng/sentence_comments/show/4', 'contributor', '/eng/sentences/show/4' ],
            [ '/eng/sentence_comments/edit/1', null, '/eng/users/login?redirect=%2Feng%2Fsentence_comments%2Fedit%2F1' ],
            [ '/eng/sentence_comments/edit/1', 'contributor', '/eng/sentences/show/4' ], // is not comment author
            [ '/eng/sentence_comments/edit/1', 'kazuki', true ], // is comment author
            [ '/eng/sentence_comments/delete_comment/1', null, '/eng/users/login?redirect=%2Feng%2Fsentence_comments%2Fdelete_comment%2F1' ],
            [ '/eng/sentence_comments/delete_comment/1', 'spammer', '/' ],
            [ '/eng/sentence_comments/delete_comment/1', 'inactive', '/' ],
            [ '/eng/sentence_comments/delete_comment/1', 'kazuki', 'https://example.net/previous_page' ],
            [ '/eng/sentence_comments/delete_comment/1', 'advanced_contributor', 'https://example.net/previous_page' ],
            [ '/eng/sentence_comments/delete_comment/1', 'corpus_maintainer', 'https://example.net/previous_page' ],
            [ '/eng/sentence_comments/delete_comment/1', 'admin', 'https://example.net/previous_page' ],
            [ '/eng/sentence_comments/of_user/kazuki', null, true ],
            [ '/eng/sentence_comments/of_user/kazuki', 'contributor', true ],
            [ '/eng/sentence_comments/of_user/non_existing_user', null, true ],
            [ '/eng/sentence_comments/on_sentences_of_user/kazuki', null, true ],
            [ '/eng/sentence_comments/on_sentences_of_user/kazuki', 'contributor', true ],
            [ '/eng/sentence_comments/on_sentences_of_user/non_existing_user', null, true ],
            [ '/eng/sentence_comments/hide_message/1', null, '/eng/users/login?redirect=%2Feng%2Fsentence_comments%2Fhide_message%2F1' ],
            [ '/eng/sentence_comments/hide_message/1', 'spammer', '/' ],
            [ '/eng/sentence_comments/hide_message/1', 'inactive', '/' ],
            [ '/eng/sentence_comments/hide_message/1', 'kazuki', '/' ],
            [ '/eng/sentence_comments/hide_message/1', 'advanced_contributor', '/' ],
            [ '/eng/sentence_comments/hide_message/1', 'corpus_maintainer', '/' ],
            [ '/eng/sentence_comments/hide_message/1', 'admin', 'https://example.net/previous_page' ],
            [ '/eng/sentence_comments/unhide_message/1', null, '/eng/users/login?redirect=%2Feng%2Fsentence_comments%2Funhide_message%2F1' ],
            [ '/eng/sentence_comments/unhide_message/1', 'spammer', '/' ],
            [ '/eng/sentence_comments/unhide_message/1', 'inactive', '/' ],
            [ '/eng/sentence_comments/unhide_message/1', 'kazuki', '/' ],
            [ '/eng/sentence_comments/unhide_message/1', 'advanced_contributor', '/' ],
            [ '/eng/sentence_comments/unhide_message/1', 'corpus_maintainer', '/' ],
            [ '/eng/sentence_comments/unhide_message/1', 'admin', 'https://example.net/previous_page' ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->configRequest([
            'headers' => ['Referer' => 'https://example.net/previous_page']
        ]);
        $this->assertAccessUrlAs($url, $user, $response);
    }

    private function saveSomething($sentenceId = 1) {
        $this->post('/eng/sentence_comments/save', [
            'sentence_id' => $sentenceId,
            'text' => 'I love this sentence!',
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
        $this->assertRedirect('/eng/sentences/show/1');
    }

    public function testSaveOnNonExistingSentence() {
        $this->logInAs('contributor');
        $this->saveSomething(999999999);
        $this->assertRedirect('/eng/sentences/show/999999999');
    }

    private function editSomething() {
        $this->put('/eng/sentence_comments/edit/1', ['text' => 'EDIT: blah blah blah']);
    }

    public function testEditAsGuest() {
        $this->enableCsrfToken();
        $this->editSomething();
        $this->assertRedirect('/eng/users/login');
    }

    public function testEditAsAuthor() {
        $this->logInAs('kazuki');
        $this->editSomething();
        $this->assertRedirect('/eng/sentences/show/4#comment-1');
    }

    public function testEditAsOtherUser() {
        $this->logInAs('advanced_contributor');
        $this->editSomething();
        $this->assertFlashMessage(
            'You do not have permission to edit this comment. '
            .'If you have received this message in error, '
            .'please contact administrators at team@tatoeba.org.'
        );
        $this->assertRedirect('/eng/sentences/show/4');
    }

    public function testEditAsAdmin() {
        $this->logInAs('admin');
        $this->editSomething();
        $this->assertRedirect('/eng/sentences/show/4#comment-1');
    }
}
