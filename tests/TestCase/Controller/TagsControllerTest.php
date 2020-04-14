<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use App\Test\TestCase\Controller\TatoebaControllerTestTrait;

class TagsControllerTest extends IntegrationTestCase {
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.audios',
        'app.favorites_users',
        'app.private_messages',
        'app.sentences',
        'app.sentences_lists',
        'app.sentences_sentences_lists',
        'app.links',
        'app.tags',
        'app.tags_sentences',
        'app.transcriptions',
        'app.users',
        'app.users_languages'
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/fra/tags/view_all', null, true ],
            [ '/fra/tags/view_all', 'contributor', true ],
            [ '/fra/tags/view_all/@', null, true ],
            [ '/fra/tags/remove_tag_from_sentence/2/2', null, '/fra/users/login?redirect=%2Ffra%2Ftags%2Fremove_tag_from_sentence%2F2%2F2' ],
            [ '/fra/tags/remove_tag_from_sentence/2/2', 'contributor', '/' ],
            [ '/fra/tags/remove_tag_from_sentence/2/2', 'advanced_contributor', '/fra/sentences/show/2' ],
            [ '/fra/tags/remove_tag_of_sentence_from_tags_show/2/2', null, '/fra/users/login?redirect=%2Ffra%2Ftags%2Fremove_tag_of_sentence_from_tags_show%2F2%2F2' ],
            [ '/fra/tags/remove_tag_of_sentence_from_tags_show/2/2', 'contributor', '/' ],
            [ '/fra/tags/remove_tag_of_sentence_from_tags_show/2/2', 'advanced_contributor', 'https://example.net/previous_page' ],
            [ '/fra/tags/show_sentences_with_tag/2', null, true ],
            [ '/fra/tags/show_sentences_with_tag/2', 'contributor', true ],
            [ '/fra/tags/show_sentences_with_tag/2/cmn', null, true ],
            [ '/fra/tags/show_sentences_with_tag/9999999999', null, '/fra/tags/view_all' ],
            [ '/fra/tags/show_sentences_with_tag/@needs_native_check', null, '/fra/tags/show_sentences_with_tag/1' ],
            [ '/fra/tags/show_sentences_with_tag/@needs_native_check/fra', null, '/fra/tags/show_sentences_with_tag/1/fra' ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->addHeader('Referer', 'https://example.net/previous_page');
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public function add_tag_post() {
        $this->ajaxPost('/fra/tags/add_tag_post', [
            'sentence_id' => 18,
            'tag_name' => 'OK',
        ]);
    }

    public function testAddTagPost_asGuest() {
        $this->enableCsrfToken();
        $this->add_tag_post();
        $this->assertResponseError();
    }

    public function testAddTagPost_asContributor() {
        $this->logInAs('contributor');
        $this->add_tag_post();
        $this->assertRedirect('/');
    }

    public function testAddTagPost_asAdvancedContributor() {
        $this->logInAs('advanced_contributor');
        $this->add_tag_post();
        $this->assertResponseOk();
    }

    public function testAddTagPost_getBackTruncatedName() {
        $this->logInAs('advanced_contributor');
        $this->ajaxPost('/eng/tags/add_tag_post', [
            'sentence_id' => 18,
            'tag_name' => '1234567890123456789012345678901234567890123456789 cut after 9',
        ]);
        $this->assertResponseNotContains(' cut after 9');
    }

    public function testSearch_asGuest() {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/fra/tags/search', ['search' => 'foo']);
        $this->assertRedirect('/fra/tags/view_all/foo');
    }

    public function testSearch_asMember() {
        $this->logInAs('contributor');
        $this->post('/fra/tags/search', ['search' => 'foo']);
        $this->assertRedirect('/fra/tags/view_all/foo');
    }
}
