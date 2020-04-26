<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

class SentencesListsControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.audios',
        'app.contributions',
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
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/eng/sentences_lists/index', null, true ],
            [ '/eng/sentences_lists/index', 'kazuki', true ],
            [ '/eng/sentences_lists/index?search=list', 'kazuki', '/eng/sentences_lists/index/list' ],
            [ '/eng/sentences_lists/index/list', 'kazuki', true ],
            [ '/eng/sentences_lists/collaborative', null, true ],
            [ '/eng/sentences_lists/collaborative', 'kazuki', true ],
            [ '/eng/sentences_lists/collaborative?search=list', 'kazuki', '/eng/sentences_lists/collaborative/list' ],
            [ '/eng/sentences_lists/collaborative/list', 'kazuki', true ],
            [ '/eng/sentences_lists/show/', null, '/eng/sentences_lists/index' ],
            [ '/eng/sentences_lists/show/1', null, true ],
            [ '/eng/sentences_lists/show/1', 'kazuki', true ],
            [ '/eng/sentences_lists/show/1/fra', 'kazuki', true ],
            [ '/eng/sentences_lists/show/3', null, '/eng/sentences_lists/index' ], // private list
            [ '/eng/sentences_lists/delete/1', null, '/eng/users/login?redirect=%2Feng%2Fsentences_lists%2Fdelete%2F1' ],
            [ '/eng/sentences_lists/delete/1', 'contributor', '/eng/sentences_lists/index' ],
            [ '/eng/sentences_lists/delete/1', 'kazuki', '/eng/sentences_lists/index' ],
            [ '/eng/sentences_lists/of_user/kazuki', null, true ],
            [ '/eng/sentences_lists/of_user/kazuki', 'contributor', true ],
            [ '/eng/sentences_lists/of_user/kazuki?username=kazuki&search=foo', 'contributor', '/eng/sentences_lists/of_user/kazuki/foo' ],
            [ '/eng/sentences_lists/of_user', 'contributor', '/eng/sentences_lists/index' ],
            [ '/eng/sentences_lists/download', null, '/eng/sentences_lists/index' ],
            [ '/eng/sentences_lists/download/1', null, true ], // unlisted public list
            [ '/eng/sentences_lists/download/1', 'contributor', true ],
            [ '/eng/sentences_lists/download/3', 'kazuki', true ], // kazuki's private list
            [ '/eng/sentences_lists/download/3', null, '/eng/sentences_lists/index' ],
            [ '/eng/sentences_lists/download/3', 'contributor', '/eng/sentences_lists/index' ],
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
            [ '/eng/sentences_lists/add_sentence_to_list/1/1', null, false ],
            [ '/eng/sentences_lists/add_sentence_to_list/1/1', 'kazuki', true ],
            [ '/eng/sentences_lists/remove_sentence_from_list/1/4', null, false ],
            [ '/eng/sentences_lists/remove_sentence_from_list/1/4', 'kazuki', true ],
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
        $this->post('/eng/sentences_lists/add', ['name' => 'My new list']);
        $this->assertRedirect('/eng/users/login');
    }

    public function testAdd_asMember() {
        $this->logInAs('contributor');
        $this->post('/eng/sentences_lists/add', ['name' => 'My new list']);
        $lists = TableRegistry::get('SentencesLists');
        $lastId = $lists->find()->orderDesc('id')->first()->id;
        $this->assertRedirect("/eng/sentences_lists/show/$lastId");
    }

    public function testAdd_fails() {
        $this->logInAs('contributor');
        $this->post('/eng/sentences_lists/add', ['name' => '']);
        $this->assertRedirect("/eng/sentences_lists/index");
    }

    public function testAddSentenceToListAsUnproperBot_bans() {
        $username = 'kazuki';
        $this->logInAs($username);
        $this->configRequest([
            'headers' => ['Referer' => 'https://tatoeba.org/eng/sentences_lists/add_new_sentence_to_list/']
        ]);

        $this->post('/eng/sentences_lists/add_new_sentence_to_list/', [
            'listId' => 1,
            'sentenceText' => 'spam',
        ]);

        $users = TableRegistry::get('Users');
        $user = $users->findByUsername($username)->first();
        $this->assertEquals(-1, $user->level);
    }

    public function testAddSentenceToListAsNormalUser_doesNotBans() {
        $username = 'kazuki';
        $this->logInAs($username);
        $this->configRequest([
            'headers' => ['Referer' => 'https://dev.tatoeba.org/eng/sentences_lists/show/1']
        ]);

        $this->post('/eng/sentences_lists/add_new_sentence_to_list/', [
            'listId' => 1,
            'sentenceText' => 'not a spam',
        ]);

        $users = TableRegistry::get('Users');
        $user = $users->findByUsername($username)->first();
        $this->assertNotEquals(-1, $user->level);
    }

    public function save_name() {
        $this->ajaxPost('/eng/sentences_lists/save_name', [
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
        $this->ajaxPost('/eng/sentences_lists/add_new_sentence_to_list/', [
            'listId' => 2,
            'sentenceText' => 'A new sentence for that list.',
        ]);
        $this->assertResponseError();
    }

    public function testAddNewSentenceToList_asOwner() {
        $this->logInAs('kazuki');
        $this->ajaxPost('/eng/sentences_lists/add_new_sentence_to_list/', [
            'listId' => 2,
            'sentenceText' => 'A new sentence for that list.',
        ]);
        $this->assertResponseOk();
    }

    public function testSetOption_asGuest() {
        $this->ajaxPost('/eng/sentences_lists/set_option/', [
            'listId' => 1,
            'option' => 'visibility',
            'value' => 'unlisted',
        ]);
        $this->assertResponseError();
    }

    public function testSetOption_asOwner() {
        $this->logInAs('kazuki');
        $this->ajaxPost('/eng/sentences_lists/set_option/', [
            'listId' => 1,
            'option' => 'visibility',
            'value' => 'unlisted',
        ]);
        $this->assertResponseOk();
    }

    public function testExportToCsv_asGuest_unlistedList() {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/eng/sentences_lists/export_to_csv', [
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
        $this->post('/eng/sentences_lists/export_to_csv', [
            'id' => 3,
            'insertId' => 1,
            'TranslationsLang' => 'eng',
        ]);
        $this->assertRedirect('/eng/sentences_lists/index');
    }
}
