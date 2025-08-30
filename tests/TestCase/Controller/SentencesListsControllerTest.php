<?php
namespace App\Test\TestCase\Controller;

use App\Model\Entity\User;
use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

class SentencesListsControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.audios',
        'app.contributions',
        'app.disabled_audios',
        'app.favorites_users',
        'app.languages',
        'app.links',
        'app.private_messages',
        'app.reindex_flags',
        'app.sentences',
        'app.sentences_lists',
        'app.sentences_sentences_lists',
        'app.transcriptions',
        'app.users',
        'app.users_languages',
        'app.users_sentences',
        'app.wiki_articles',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/sentences_lists/index', null, true ],
            [ '/en/sentences_lists/index', 'kazuki', true ],
            [ '/en/sentences_lists/index?search=list', 'kazuki', '/en/sentences_lists/index/list' ],
            [ '/en/sentences_lists/index/list', 'kazuki', true ],
            [ '/en/sentences_lists/collaborative', null, true ],
            [ '/en/sentences_lists/collaborative', 'kazuki', true ],
            [ '/en/sentences_lists/collaborative?search=list', 'kazuki', '/en/sentences_lists/collaborative/list' ],
            [ '/en/sentences_lists/collaborative/list', 'kazuki', true ],
            [ '/en/sentences_lists/show/', null, '/en/sentences_lists/index' ],
            [ '/en/sentences_lists/show/1', null, true ],
            [ '/en/sentences_lists/show/1', 'kazuki', true ],
            [ '/en/sentences_lists/show/1/und/fra', 'kazuki', true ],
            [ '/en/sentences_lists/show/3', null, '/en/sentences_lists/index' ], // private list
            [ '/en/sentences_lists/delete/1', null, '/en/users/login?redirect=%2Fen%2Fsentences_lists%2Fdelete%2F1' ],
            [ '/en/sentences_lists/delete/1', 'contributor', '/en/sentences_lists/index' ],
            [ '/en/sentences_lists/delete/1', 'kazuki', '/en/sentences_lists/index' ],
            [ '/en/sentences_lists/of_user/kazuki', null, true ],
            [ '/en/sentences_lists/of_user/kazuki', 'contributor', true ],
            [ '/en/sentences_lists/of_user/kazuki?username=kazuki&search=foo', 'contributor', '/en/sentences_lists/of_user/kazuki/foo' ],
            [ '/en/sentences_lists/of_user', 'contributor', '/en/sentences_lists/index' ],
            [ '/en/sentences_lists/download', null, '/en/sentences_lists/index' ],
            [ '/en/sentences_lists/download/1', null, true ], // unlisted public list
            [ '/en/sentences_lists/download/1', 'contributor', true ],
            [ '/en/sentences_lists/download/3', 'kazuki', true ], // kazuki's private list
            [ '/en/sentences_lists/download/3', null, '/en/sentences_lists/index' ],
            [ '/en/sentences_lists/download/3', 'contributor', '/en/sentences_lists/index' ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public function ajaxAccessesProvider() {
        return [
            [ '/en/sentences_lists/add_sentence_to_list/1/1', null, false ],
            [ '/en/sentences_lists/add_sentence_to_list/1/1', 'kazuki', true ],
            [ '/en/sentences_lists/remove_sentence_from_list/1/4', null, false ],
            [ '/en/sentences_lists/remove_sentence_from_list/1/4', 'kazuki', true ],
        ];
    }

    /**
     * @dataProvider ajaxAccessesProvider
     */
    public function testControllerAjaxAccess($url, $user, $response) {
        $this->assertAjaxAccessUrlAs($url, $user, $response);
    }

    public function testAdd_asGuest() {
        $this->enableCsrfToken();
        $this->post('/en/sentences_lists/add', ['name' => 'My new list']);
        $this->assertRedirect('/en/users/login');
    }

    public function testAdd_asMember() {
        $this->logInAs('contributor');
        $this->post('/en/sentences_lists/add', ['name' => 'My new list']);
        $lists = TableRegistry::get('SentencesLists');
        $lastId = $lists->find()->orderDesc('id')->first()->id;
        $this->assertRedirect("/en/sentences_lists/show/$lastId");
    }

    public function testAdd_fails() {
        $this->logInAs('contributor');
        $this->post('/en/sentences_lists/add', ['name' => '']);
        $this->assertRedirect("/en/sentences_lists/index");
    }

    public function testAddSentenceToListAsUnproperBot_bans() {
        $username = 'kazuki';
        $this->logInAs($username);
        $this->configRequest([
            'headers' => ['Referer' => 'https://tatoeba.org/eng/sentences_lists/add_new_sentence_to_list/']
        ]);

        $this->post('/en/sentences_lists/add_new_sentence_to_list/', [
            'listId' => 1,
            'sentenceText' => 'spam',
        ]);

        $users = TableRegistry::get('Users');
        $user = $users->findByUsername($username)->first();
        $this->assertEquals(-1, $user->level);
        $lists = TableRegistry::get('SentencesLists');
        $list = $lists->get(1);
        $this->assertEquals('private', $list->visibility);
    }

    public function testAddSentenceToListAsNormalUser_doesNotBans() {
        $username = 'kazuki';
        $this->logInAs($username);
        $this->configRequest([
            'headers' => ['Referer' => 'https://dev.tatoeba.org/eng/sentences_lists/show/1']
        ]);

        $this->post('/en/sentences_lists/add_new_sentence_to_list/', [
            'listId' => 1,
            'sentenceText' => 'not a spam',
        ]);

        $users = TableRegistry::get('Users');
        $user = $users->findByUsername($username)->first();
        $this->assertNotEquals(-1, $user->level);
        $lists = TableRegistry::get('SentencesLists');
        $list = $lists->get(1);
        $this->assertNotEquals('private', $list->visibility);
    }

    public function save_name() {
        $this->ajaxPost('/en/sentences_lists/save_name', [
            'value' => 'New name',
            'id' => 'l1',
        ]);
    }

    public function testSaveName_asGuest() {
        $this->save_name();
        $this->assertResponseError();
    }

    public function testSaveName_asOwner() {
        $this->logInAs('kazuki');
        $this->save_name();
        $this->assertResponseOk();
        $this->assertResponseEquals('New name');
    }

    public function testSaveName_asNonOwner() {
        $this->logInAs('admin');
        $this->save_name();
        $this->assertResponseOk();
        $this->assertResponseEquals('error');
    }

    public function testAddNewSentenceToList_asGuest() {
        $this->ajaxPost('/en/sentences_lists/add_new_sentence_to_list/', [
            'listId' => 2,
            'sentenceText' => 'A new sentence for that list.',
        ]);
        $this->assertResponseError();
    }

    public function testAddNewSentenceToList_asOwner() {
        $this->logInAs('kazuki');
        $this->ajaxPost('/en/sentences_lists/add_new_sentence_to_list/', [
            'listId' => 2,
            'sentenceText' => 'A new sentence for that list.',
        ]);
        $this->assertResponseOk();
    }

    public function testSetOption_asGuest() {
        $this->ajaxPost('/en/sentences_lists/set_option/', [
            'listId' => 1,
            'option' => 'visibility',
            'value' => 'unlisted',
        ]);
        $this->assertResponseError();
    }

    public function testSetOption_asOwner() {
        $this->logInAs('kazuki');
        $this->ajaxPost('/en/sentences_lists/set_option/', [
            'listId' => 1,
            'option' => 'visibility',
            'value' => 'unlisted',
        ]);
        $this->assertResponseOk();
    }

    public function testExportToCsv_asGuest_unlistedList() {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/en/sentences_lists/export_to_csv', [
            'id' => 1,
            'insertId' => 1,
            'TranslationsLang' => 'eng',
        ]);
        $this->assertResponseOk();
        $this->assertHeader('Content-disposition', 'attachment;filename=export_list_1eng.csv');
        $this->assertHeader('Content-type',        'application/vnd.ms-excel');
    }

    public function testExportToCsv_asGuest_privateList() {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/en/sentences_lists/export_to_csv', [
            'id' => 3,
            'insertId' => 1,
            'TranslationsLang' => 'eng',
        ]);
        $this->assertRedirect('/en/sentences_lists/index');
    }

    //test redirect /:id/langX maps to /:id/und/langX
    public function testShowSentenceListRedirect()
    {
        $lists = TableRegistry::get('SentencesLists');
        $lastId = $lists->find()->orderDesc('id')->first()->id;
        $this->get("/en/sentences_lists/show/$lastId/cmn");
        $this->assertResponseCode(301);
        $this->assertRedirectContains("/en/sentences_lists/show/$lastId/und/cmn");
    }

    private function addSentencesToList($listId, $nbSentences, $userId = 1) {
        $newSentences = [];
        for ($i = 1; $i <= $nbSentences; $i++) {
            $newSentences[] = [
                'lang' => 'eng',
                'text' => "Test sentence number $i.",
                'user_id' => $userId,
            ];
        }
        $sentences = TableRegistry::getTableLocator()->get('Sentences');
        $entities = $sentences->newEntities($newSentences);
        $sentences->saveMany($entities);

        $sentencesLists = TableRegistry::getTableLocator()->get('SentencesLists');
        $sentencesLists->addSentencesToList($entities, $listId, $userId);

        return $sentencesLists->getNumberOfSentences($listId);
    }

    public function testPaginateRedirectsPageOutOfBoundsToLastPage_asGuest() {
        $listId = 5;
        $defaultNbPerPage = User::$defaultSettings['sentences_per_page'];

        $nbSentences = $this->addSentencesToList($listId, $defaultNbPerPage * 2 + 1);

        $lastPage = ceil($nbSentences / $defaultNbPerPage);
        $this->assertEquals(3, $lastPage);

        $this->get("/en/sentences_lists/show/$listId/und/und?page=9999999");
        $this->assertRedirect("/en/sentences_lists/show/$listId/und/und?page=$lastPage");
    }

    public function testPaginateRedirectsPageOutOfBoundsToLastPage_withUserSetting() {
        $listId = 5;
        $user = 'kazuki';
        $userId = 7;
        $users = TableRegistry::getTableLocator()->get('Users');
        $nbPerPageSetting = $users->getSettings($userId)['settings']['sentences_per_page'];

        $nbSentences = $this->addSentencesToList($listId, $nbPerPageSetting * 2 + 1);

        $lastPage = ceil($nbSentences / $nbPerPageSetting);
        $this->assertEquals(3, $lastPage);

        $this->logInAs($user);
        $this->get("/en/sentences_lists/show/$listId/und/und?page=9999999");
        $this->assertRedirect("/en/sentences_lists/show/$listId/und/und?page=$lastPage");
    }

}
