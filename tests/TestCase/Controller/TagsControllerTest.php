<?php
namespace App\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;
use App\Model\Entity\User;
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
        'app.users_languages',
        'app.users_sentences',
        'app.wiki_articles',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/tags/view_all', null, true ],
            [ '/en/tags/view_all', 'contributor', true ],
            [ '/en/tags/view_all/@', null, true ],
            [ '/en/tags/remove_tag_from_sentence/2/2', null, '/en/users/login?redirect=%2Fen%2Ftags%2Fremove_tag_from_sentence%2F2%2F2' ],
            [ '/en/tags/remove_tag_from_sentence/2/2', 'contributor', '/' ],
            [ '/en/tags/remove_tag_from_sentence/2/2', 'advanced_contributor', '/en/sentences/show/2' ],
            [ '/en/tags/remove_tag_of_sentence_from_tags_show/2/2', null, '/en/users/login?redirect=%2Fen%2Ftags%2Fremove_tag_of_sentence_from_tags_show%2F2%2F2' ],
            [ '/en/tags/remove_tag_of_sentence_from_tags_show/2/2', 'contributor', '/' ],
            [ '/en/tags/remove_tag_of_sentence_from_tags_show/2/2', 'advanced_contributor', 'https://example.net/previous_page' ],
            [ '/en/tags/show_sentences_with_tag/2', null, true ],
            [ '/en/tags/show_sentences_with_tag/2', 'contributor', true ],
            [ '/en/tags/show_sentences_with_tag/2/cmn', null, true ],
            [ '/en/tags/show_sentences_with_tag/9999999999', null, '/en/tags/view_all' ],
            [ '/en/tags/show_sentences_with_tag/@needs_native_check', null, '/en/tags/show_sentences_with_tag/1' ],
            [ '/en/tags/show_sentences_with_tag/@needs_native_check/eng', null, '/en/tags/show_sentences_with_tag/1/eng' ],
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
            [ '/en/tags/autocomplete/foobar', null, true ],
            [ '/en/tags/autocomplete/foobar', 'contributor', true ],
        ];
    }

    /**
     * @dataProvider ajaxAccessesProvider
     */
    public function testControllerAjaxAccess($url, $user, $response) {
        $this->assertAjaxAccessUrlAs($url, $user, $response);
    }

    public function add_tag_post() {
        $this->ajaxPost('/fr/tags/add_tag_post', [
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
        $this->ajaxPost('/en/tags/add_tag_post', [
            'sentence_id' => 18,
            'tag_name' => '1234567890123456789012345678901234567890123456789 cut after 9',
        ]);
        $this->assertResponseNotContains(' cut after 9');
    }

    public function testAddTagPost_duplicateTagReturnsEmptyResponse ()
    {
        $this->logInAs('advanced_contributor');
        $this->ajaxPost('/en/tags/add_tag_post', [
            'sentence_id' => 8,
            'tag_name' => '@needs native check',
        ]);
        $this->assertResponseEmpty();
    }

    public function testSearch_asGuest() {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/fr/tags/search', ['search' => 'foo']);
        $this->assertRedirect('/fr/tags/view_all/foo');
    }

    public function testSearch_asMember() {
        $this->logInAs('contributor');
        $this->post('/fr/tags/search', ['search' => 'foo']);
        $this->assertRedirect('/fr/tags/view_all/foo');
    }

    private function addSentencesWithTag($tagId, $nbSentences, $userId = 1) {
        $newSentences = [];
        for ($i = 1; $i <= $nbSentences; $i++) {
            $newSentences[] = [
                'lang' => 'eng',
                'text' => "Test sentence number $i.",
                'user_id' => $userId,
            ];
        }
        $Sentences = TableRegistry::getTableLocator()->get('Sentences');
        $entities = $Sentences->newEntities($newSentences);
        $Sentences->saveMany($entities);

        $Tags = TableRegistry::getTableLocator()->get('Tags');
        foreach ($entities as $sentence) {
            $Tags->TagsSentences->tagSentence($sentence->id, $tagId, $userId);
        }

        return $Tags->TagsSentences->find()->where(['tag_id' => $tagId])->count();
    }

    public function testPaginateRedirectsPageOutOfBoundsToLastPage_asGuest() {
        $tagId = 2;
        $defaultNbPerPage = User::$defaultSettings['sentences_per_page'];

        $nbSentences = $this->addSentencesWithTag($tagId, $defaultNbPerPage * 2 + 1);

        $lastPage = ceil($nbSentences / $defaultNbPerPage);
        $this->assertEquals(3, $lastPage);

        $this->get("/en/tags/show_sentences_with_tag/$tagId?page=9999999");
        $this->assertRedirect("/en/tags/show_sentences_with_tag/$tagId?page=$lastPage");
    }

    public function testPaginateRedirectsPageOutOfBoundsToLastPage_withUserSetting() {
        $tagId = 2;
        $user = 'kazuki';
        $userId = 7;
        $nbPerPageSetting = TableRegistry::getTableLocator()
            ->get('Users')
            ->getSettings($userId)
            ['settings']['sentences_per_page'];

        $nbSentences = $this->addSentencesWithTag($tagId, $nbPerPageSetting * 2 + 1);

        $lastPage = ceil($nbSentences / $nbPerPageSetting);
        $this->assertEquals(3, $lastPage);

        $this->logInAs($user);
        $this->get("/en/tags/show_sentences_with_tag/$tagId?page=9999999");
        $this->assertRedirect("/en/tags/show_sentences_with_tag/$tagId?page=$lastPage");
    }
}
