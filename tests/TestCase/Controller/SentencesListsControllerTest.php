<?php
namespace App\Test\TestCase\Controller;

use App\Model\Entity\User;
use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestCase;
use Helmich\JsonAssert\JsonAssertions;

class SentencesListsControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;
    use JsonAssertions;

    public $fixtures = [
        'app.Audios',
        'app.Contributions',
        'app.DisabledAudios',
        'app.FavoritesUsers',
        'app.Languages',
        'app.Links',
        'app.PrivateMessages',
        'app.ReindexFlags',
        'app.Sentences',
        'app.SentencesLists',
        'app.SentencesSentencesLists',
        'app.Transcriptions',
        'app.Users',
        'app.UsersLanguages',
        'app.UsersSentences',
        'app.WikiArticles',
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
            [ '/en/sentences_lists/show/1?sort=created&direction=asc', null, '/en/sentences_lists/show/1?sort=id&direction=asc'],
            [ '/en/sentences_lists/show/1/und/fra?sort=created&direction=asc', null, '/en/sentences_lists/show/1/und/fra?sort=id&direction=asc'],
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
            [ '/en/sentences_lists/choices', null, '/en/users/login?redirect=%2Fen%2Fsentences_lists%2Fchoices' ],
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
    public function testControllerNonAngularAjaxAccess($url, $user, $response) {
        $this->assertAjaxAccessUrlAs($url, $user, $response);
    }

    /**
     * @dataProvider ajaxAccessesProvider
     */
    public function testControllerAngularAjaxAccess($url, $user, $response) {
        $this->addHeader('Accept', 'application/json');
        $this->assertAjaxAccessUrlAs($url, $user, $response);
    }

    public function testAdd_asGuest() {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/en/sentences_lists/add', ['name' => 'My new list']);
        $this->assertRedirect('/en/users/login');
    }

    public function testAdd_asMember() {
        $this->logInAs('contributor');
        $this->post('/en/sentences_lists/add', ['name' => 'My new list']);
        $lists = $this->getTableLocator()->get('SentencesLists');
        $lastId = $lists->find()->orderDesc('id')->first()->id;
        $this->assertRedirect("/en/sentences_lists/show/$lastId");
    }

    public function testAdd_fails() {
        $this->logInAs('contributor');
        $this->post('/en/sentences_lists/add', ['name' => '']);
        $this->assertRedirect("/en/sentences_lists/index");
    }

    public function testAddSentenceToListAsUnproperBot_bans() {
        \Cake\Core\Configure::write('App.fullBaseUrl', 'https://tatoeba.org');
        $username = 'kazuki';
        $this->logInAs($username);
        $this->configRequest([
            'headers' => ['Referer' => 'https://tatoeba.org/eng/sentences_lists/add_new_sentence_to_list/']
        ]);

        $this->post('/en/sentences_lists/add_new_sentence_to_list/', [
            'listId' => 1,
            'sentenceText' => 'spam',
        ]);

        $users = $this->getTableLocator()->get('Users');
        $user = $users->findByUsername($username)->first();
        $this->assertEquals(-1, $user->level);
        $lists = $this->getTableLocator()->get('SentencesLists');
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

        $users = $this->getTableLocator()->get('Users');
        $user = $users->findByUsername($username)->first();
        $this->assertNotEquals(-1, $user->level);
        $lists = $this->getTableLocator()->get('SentencesLists');
        $list = $lists->get(1);
        $this->assertNotEquals('private', $list->visibility);
    }

    public function save_name() {
        $this->ajaxPost('/en/sentences_lists/save_name', [
            'value' => 'New name',
            'id' => 'l1',
        ]);
    }

    public function save_name_angular() {
        $this->addHeader('Accept', 'application/json');
        $this->ajaxPost('/en/sentences_lists/save_name', [
            'name' => 'New name',
            'id' => '1',
        ]);
    }

    public function testSaveName_asGuest() {
        $this->save_name();
        $this->assertResponseError();
    }

    public function testSaveName_asOwner_nonAngular() {
        $this->logInAs('kazuki');

        $this->save_name();

        $this->assertResponseOk();
        $this->assertResponseEquals('New name');
    }

    public function testSaveName_asOwner_angular() {
        $this->logInAs('kazuki');

        $this->save_name_angular();

        $this->assertResponseOk();
        $actual = $this->_getBodyAsString();
        $this->assertJsonValueEquals($actual, '$.result', 'New name');
    }

    public function testSaveName_asNonOwner_nonAngular() {
        $this->logInAs('admin');

        $this->save_name();

        $this->assertResponseOk();
        $this->assertResponseEquals('error');
    }

    public function testSaveName_asNonOwner_angular() {
        $this->logInAs('admin');

        $this->save_name_angular();

        $this->assertResponseOk();
        $actual = $this->_getBodyAsString();
        $this->assertJsonValueEquals($actual, '$.result', 'error');
    }

    public function testAddNewSentenceToList_asGuest() {
        $this->ajaxPost('/en/sentences_lists/add_new_sentence_to_list/', [
            'listId' => 2,
            'sentenceText' => 'A new sentence for that list.',
        ]);
        $this->assertResponseError();
    }

    public function testAddNewSentenceToList_asOwner_nonAngular() {
        $this->logInAs('kazuki');
        $this->ajaxPost('/en/sentences_lists/add_new_sentence_to_list/', [
            'listId' => 2,
            'sentenceText' => 'A new sentence for that list.',
        ]);
        $this->assertResponseOk();
    }

    public function testAddNewSentenceToList_asOwner_angular() {
        $this->addHeader('Accept', 'application/json');

        $this->testAddNewSentenceToList_asOwner_nonAngular();

        $actual = $this->_getBodyAsString();
        $expected = [
            '$' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.sentence.text' => 'A new sentence for that list.',
            '$.sentence.lang' => null,
            '$.sentence.user.username' => 'kazuki',
            '$.sentence.sentences_lists' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.sentence.sentences_lists[0].id' => 2,
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }

    public function testAddSentenceToNewList_OK() {
        $this->logInAs('kazuki');

        $this->ajaxPost('/en/sentences_lists/add_sentence_to_new_list', [
            'name' => 'A new list for sentence 1',
            'sentenceId' => 1,
        ]);

        $this->assertResponseOk();
        $actual = $this->_getBodyAsString();
        $expected = [
            '$' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.result.name' => 'A new list for sentence 1',
            '$.result.user_id' => 7,
            '$.result.hasSentence' => true,
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }

    public function testAddSentenceToNewList_error() {
        $this->logInAs('kazuki');

        $this->ajaxPost('/en/sentences_lists/add_sentence_to_new_list', [
            'name' => 'A new list for a sentence that does not exist',
            'sentenceId' => 99999999999999,
        ]);

        $this->assertResponseOk();
        $actual = $this->_getBodyAsString();
        $this->assertJsonValueEquals($actual, '$.result', 'error');
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

    public function testChoices_asMember() {
        $this->logInAs('kazuki');

        $this->get('/en/sentences_lists/choices');

        $this->assertResponseOk();
        $actual = $this->_getBodyAsString();
        $expected = [
            '$' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.lists' => new \PHPUnit\Framework\Constraint\Count(4),
            '$.lists[0].id' => 2,
            '$.lists[0].name' => 'Public list',
            '$.lists[0].user_id' => 7,
            '$.lists[0].is_mine' => '1',
            '$.lists[0].is_collaborative' => '0',
            '$.lists[1].id' => 3,
            '$.lists[2].id' => 1,
            '$.lists[3].id' => 5,
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
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
        $lists = $this->getTableLocator()->get('SentencesLists');
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
        $sentences = $this->getTableLocator()->get('Sentences');
        $entities = $sentences->newEntities($newSentences);
        $sentences->saveMany($entities);

        $sentencesLists = $this->getTableLocator()->get('SentencesLists');
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
        $users = $this->getTableLocator()->get('Users');
        $nbPerPageSetting = $users->getSettings($userId)['settings']['sentences_per_page'];

        $nbSentences = $this->addSentencesToList($listId, $nbPerPageSetting * 2 + 1);

        $lastPage = ceil($nbSentences / $nbPerPageSetting);
        $this->assertEquals(3, $lastPage);

        $this->logInAs($user);
        $this->get("/en/sentences_lists/show/$listId/und/und?page=9999999");
        $this->assertRedirect("/en/sentences_lists/show/$listId/und/und?page=$lastPage");
    }

}
