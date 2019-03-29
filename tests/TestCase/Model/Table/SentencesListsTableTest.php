<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SentencesListsTable;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use App\Model\CurrentUser;
use Cake\Utility\Hash;

class SentencesListsTableTest extends TestCase {
    public $fixtures = array(
        'app.sentences_lists',
        'app.sentences_sentences_lists',
        'app.sentences',
        'app.favorites_users',
        'app.users',
        'app.users_languages',
        'app.contributions',
        'app.languages',
        'app.reindex_flags',
        'app.links',
        'app.audios',
        'app.transcriptions'
    );

    function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
        $this->SentencesList = TableRegistry::getTableLocator()->get('SentencesLists');
    }

    function tearDown() {
        unset($this->SentencesList);
        parent::tearDown();
    }
    
    function testGetListWithPermissions_listBelongsToCurrentUser() {
        $list = $this->SentencesList->getListWithPermissions(1, 7);
        $expected = array(
            'canView' => true,
            'canEdit' => true,
            'canAddSentences' => true,
            'canRemoveSentences' => true,
            'canDownload' => true
        );
        $this->assertEquals($expected, $list['Permissions']);
    }

    function testGetListWithPermissions_listDoesntBelongsToCurrentUser() {
        $list = $this->SentencesList->getListWithPermissions(1, 4);
        $expected = array(
            'canView' => true,
            'canEdit' => false,
            'canAddSentences' => false,
            'canRemoveSentences' => false,
            'canDownload' => true
        );
        $this->assertEquals($expected, $list['Permissions']);
    }

    function testCreateList_succeeds() {
        $userId = 4;
        $name = 'My new shiny list';
        $list = $this->SentencesList->createList($name, $userId);

        $expected = array(
            'name' => $name,
            'user_id' => $userId
        );
        $result = array_intersect_key($list->toArray(), $expected);

        $this->assertEquals($expected, $result);
    }

    function testCreateList_fails() {
        $userId = 4;
        $name = '   ';
        $list = $this->SentencesList->createList($name, $userId);

        $this->assertFalse($list);
    }

    function testDeleteList_succeeds() {
        $list = $this->SentencesList->deleteList(1, 7);

        $this->assertTrue($list);
    }

    function testDeleteList_fails() {
        $list = $this->SentencesList->deleteList(1, 1);

        $this->assertFalse($list);
    }

    function testEmptyList_succeeds() {
        $listId = 1;

        $result = $this->SentencesList->emptyList($listId, 7);
        $this->assertTrue($result);

        $after = $this->SentencesList->SentencesSentencesLists->find()
                      ->where(['sentences_list_id' => $listId])->all();
        $this->assertEmpty($after);

        $count = $this->SentencesList->get($listId)->numberOfSentences;
        $this->assertEquals(0, $count);

        $all = $this->SentencesList->find()->all();
        $this->assertNotEmpty($all);
    }

    function testEmptyList_fails() {
        $listId = 1;
        $before = $this->SentencesList->SentencesSentencesLists->find()
                       ->where(['sentences_list_id' => $listId])->all();

        $result = $this->SentencesList->emptyList(1, 1);
        $this->assertFalse($result);

        $after = $this->SentencesList->SentencesSentencesLists->find()
                      ->where(['sentences_list_id' => $listId])->all();
        $this->assertEquals($before, $after);
    }

    function testEditName_suceeds() {
        $listId = 1;
        $newName = 'Very interesting French sentences';    
        $list = $this->SentencesList->editName($listId, $newName, 7);
        
        $expected = array(
            'id' => $listId,
            'name' => $newName
        );
        $result = array_intersect_key($list['SentencesList'], $expected);

        $this->assertEquals($result, $expected);
    }

    function testEditName_fails() {
        $list = $this->SentencesList->editName(1, 'x', 1);

        $this->assertFalse($list);
    }

    function testEditOption_succeeds() {
        $list = $this->SentencesList->editOption(1, 'editable_by', 'everyone', 7);
        $expected = array(
            'id' => 1,
            'editable_by' => 'everyone'
        );
        $result = array_intersect_key($list['SentencesList'], $expected);

        $this->assertEquals($result, $expected);
    }

    function testEditOption_fails() {
        $list = $this->SentencesList->editOption(1, 'editable', 'everyone', 7);
        $this->assertEmpty($list);
    }

    function testAddSentenceToList_succeeds() {
        $before = $this->SentencesList->SentencesSentencesLists->find()
            ->where(['sentence_id' => 12, 'sentences_list_id' => 1])
            ->count();
        $result = $this->SentencesList->addSentenceToList(12, 1, 7);
        $after = $this->SentencesList->SentencesSentencesLists->find()
            ->where(['sentence_id' => 12, 'sentences_list_id' => 1])
            ->count();

        $this->assertTrue($result);
        $this->assertEquals(1, $after - $before);
    }

    function testAddSentenceToList_incrementsCount() {
        $before = $this->SentencesList->get(1)->numberOfSentences;
        $this->SentencesList->addSentenceToList(1, 1, 7);
        $after = $this->SentencesList->get(1)->numberOfSentences;

        $this->assertEquals(1, $after - $before);
    }

    function testAddSentenceToList_failsBecauseUserNotAllowed() {
        $before = $this->SentencesList->SentencesSentencesLists->find()
            ->where(['sentence_id' => 12, 'sentences_list_id' => 1])
            ->count();
        $result = $this->SentencesList->addSentenceToList(4, 1, 1);
        $after = $this->SentencesList->SentencesSentencesLists->find()
            ->where(['sentence_id' => 12, 'sentences_list_id' => 1])
            ->count();
        
        $this->assertFalse($result);
        $this->assertEquals($after, $before);
    }

    function testAddSentenceToList_failsBecauseSentenceAlreadyInList() {
        $result = $this->SentencesList->addSentenceToList(4, 1, 7);

        $this->assertFalse($result);
    }

    function testAddSentenceToList_failsBecauseUnknownSentenceId() {
        $result = $this->SentencesList->addSentenceToList(10000, 1, 7);

        $this->assertFalse($result);
    }


    function testAddSentencesToList_succeeds() {
        $listId = 1;
        $sentences = $this->SentencesList->Sentences->find()
                          ->where(['user_id' => 1])->toList();
        $before = $this->SentencesList->SentencesSentencesLists->find()
            ->where(['sentences_list_id' => $listId])
            ->count();

        $result = $this->SentencesList->addSentencesToList($sentences, $listId, 7);
        $this->assertTrue($result);

        $after = $this->SentencesList->SentencesSentencesLists->find()
            ->where(['sentences_list_id' => $listId])
            ->count();
        $this->assertEquals(count($sentences), $after - $before);
    }

    function testAddSentencesToList_incrementsCount() {
        $listId = 1;
        $sentences = $this->SentencesList->Sentences->find()
                          ->where(['user_id' => 1])->toList();
        $before = $this->SentencesList->get($listId)->numberOfSentences;

        $this->SentencesList->addSentencesToList($sentences, $listId, 7);

        $after = $this->SentencesList->get($listId)->numberOfSentences;
        $this->assertEquals(count($sentences), $after - $before);
    }

    function testAddSentencesToList_failsBecauseSentenceAlreadyInList() {
        $sentences = $this->SentencesList->Sentences->find()
                          ->where(['user_id' => 7])->toList();
        $result = $this->SentencesList->addSentencesToList($sentences, 1, 7);

        $this->assertFalse($result);
    }


    function testAddNewSentenceToList_succeeds() {
        $listId = 1;
        $lang = 'eng';
        $text = 'This is a new shiny sentence.';
        $userId = 7;
        $data = $this->SentencesList->addNewSentenceToList($listId, $text, $lang, $userId);
        
        $expected = [$lang, $text, $userId];
        $result = [$data->lang, $data->text, $data->user->id];
        
        $this->assertEquals($expected, $result);
    }

    function testAddNewSentenceToList_failsBecauseEmptySentence() {
        $result = $this->SentencesList->addNewSentenceToList(1, '', 'eng', 7);

        $this->assertFalse($result);
    }

    function testAddNewSentenceToList_failsBecauseUserNotAllowed() {
        $result = $this->SentencesList->addNewSentenceToList(1, 'x', 'eng', 1);

        $this->assertFalse($result);
    }

    function testRemoveSentenceFromList() {
        $sentenceId = 4;
        $listId = 1;
        $userId = 7;
        $before = $this->SentencesList->SentencesSentencesLists->find()
            ->where(['sentences_list_id' => $listId])
            ->count();
        $this->SentencesList->removeSentenceFromList($sentenceId, $listId, $userId);
        $after = $this->SentencesList->SentencesSentencesLists->find()
            ->where(['sentences_list_id' => $listId])
            ->count();
        $this->assertEquals(1, $before - $after);

        $list = $this->SentencesList->get($listId);
        $this->assertEquals($after, $list->numberOfSentences);
    }

    function testRemoveSentenceFromList_succeeds() {
        $result = $this->SentencesList->removeSentenceFromList(4, 1, 7);

        $this->assertTrue($result);
    }

    function testRemoveSentenceFromList_failsBecauseUnknownSentenceId() {
        $result = $this->SentencesList->removeSentenceFromList(10000, 1, 7);

        $this->assertFalse($result);
    }

    function testRemoveSentenceFromList_failsBecauseUserNotAllowed() {
        $result = $this->SentencesList->removeSentenceFromList(4, 1, 1);

        $this->assertFalse($result);
    }

    function testRemoveSentenceFromList_failsBecauseUnknownListId() {
        $result = $this->SentencesList->removeSentenceFromList(4, 999999, 7);

        $this->assertFalse($result);
    }

    function testGetUserChoices() {
        CurrentUser::store(['id' => 7]);
        $result = $this->SentencesList->getUserChoices(7);
        $expected = [
            'OfUser' => [
                '1' => 'Interesting French sentences',
                '2' => 'Public list',
                '3' => 'Private list'
            ],
            'Collaborative' => []
        ];
        $this->assertEquals($expected, $result);
    }

    function testGetSearchableLists_asGuests() {
        CurrentUser::store(['id' => null]);
        $result = $this->SentencesList->getSearchableLists();
        $this->assertEquals([2], Hash::extract($result, '{n}.id'));
    }

    function testGetSearchableLists_asMember() {
        CurrentUser::store(['id' => 7]);
        $result = $this->SentencesList->getSearchableLists();
        $this->assertEquals([1, 3, 2], Hash::extract($result, '{n}.id'));
    }

    function testGetNameForListWithId() {
        $result = $this->SentencesList->getNameForListWithId(1);
        $this->assertEquals('Interesting French sentences', $result);
    }

    function testGetNumberOfSentences() {
        $result = $this->SentencesList->getNumberOfSentences(1);
        $this->assertEquals(2, $result);
    }

    function testGetSentencesAndTranslationsOnly_withoutTranslations() {
        $result = $this->SentencesList->getSentencesAndTranslationsOnly(1);
        $ids = Hash::extract($result, '{n}.id');
        $texts = Hash::extract($result, '{n}.text');
        $this->assertEquals([4, 8], $ids);
        $this->assertEquals(count($ids), count($texts));
    }

    function testGetSentencesAndTranslationsOnly_withTranslations() {
        $result = $this->SentencesList->getSentencesAndTranslationsOnly(1, 'eng');
        $ids = Hash::extract($result, '{n}.id');
        $translations = Hash::extract($result, '{n}.translation');
        $this->assertEquals([4], $ids);
        $this->assertEquals(count($ids), count($translations));
    }

    function testIsSearchableList_isSearchable() {
        $result = $this->SentencesList->isSearchableList(1);
        $expected = [
            'id' => 1,
            'user_id' => 7,
            'name' => 'Interesting French sentences'
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    function testIsSearchableList_isNotSearchable() {
        CurrentUser::store(null);
        $result = $this->SentencesList->isSearchableList(3);
        $this->assertNull($result);
    }
}
