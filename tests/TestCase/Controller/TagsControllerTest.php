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
            [ '/eng/tags/view_all', null, true ],
            [ '/eng/tags/view_all', 'contributor', true ],
            [ '/eng/tags/view_all/@', null, true ],
            [ '/eng/tags/remove_tag_from_sentence/2/2', null, '/eng/users/login?redirect=%2Feng%2Ftags%2Fremove_tag_from_sentence%2F2%2F2' ],
            [ '/eng/tags/remove_tag_from_sentence/2/2', 'contributor', '/' ],
            [ '/eng/tags/remove_tag_from_sentence/2/2', 'advanced_contributor', '/eng/sentences/show/2' ],
            [ '/eng/tags/remove_tag_of_sentence_from_tags_show/2/2', null, '/eng/users/login?redirect=%2Feng%2Ftags%2Fremove_tag_of_sentence_from_tags_show%2F2%2F2' ],
            [ '/eng/tags/remove_tag_of_sentence_from_tags_show/2/2', 'contributor', '/' ],
            [ '/eng/tags/remove_tag_of_sentence_from_tags_show/2/2', 'advanced_contributor', 'https://example.net/previous_page' ],
            [ '/eng/tags/show_sentences_with_tag/2', null, true ],
            [ '/eng/tags/show_sentences_with_tag/2', 'contributor', true ],
            [ '/eng/tags/show_sentences_with_tag/2/cmn', null, true ],
            [ '/eng/tags/show_sentences_with_tag/9999999999', null, '/eng/tags/view_all' ],
            [ '/eng/tags/show_sentences_with_tag/@needs_native_check', null, '/eng/tags/show_sentences_with_tag/1' ],
            [ '/eng/tags/show_sentences_with_tag/@needs_native_check/eng', null, '/eng/tags/show_sentences_with_tag/1/eng' ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->addHeader('Referer', 'https://example.net/previous_page');
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public function ajaxAccessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/eng/tags/autocomplete/foobar', null, true ],
            [ '/eng/tags/autocomplete/foobar', 'contributor', true ],
        ];
    }

    /**
     * @dataProvider ajaxAccessesProvider
     */
    public function testControllerAjaxAccess($url, $user, $response) {
        $this->assertAjaxAccessUrlAs($url, $user, $response);
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

    public function testAddTagPost_duplicateTagReturnsEmptyResponse ()
    {
        $this->logInAs('advanced_contributor');
        $this->ajaxPost('/eng/tags/add_tag_post', [
            'sentence_id' => 8,
            'tag_name' => '@needs native check',
        ]);
        $this->assertResponseEmpty();
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
