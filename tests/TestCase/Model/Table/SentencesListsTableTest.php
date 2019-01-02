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
        $result = array_intersect_key($list['SentencesList'], $expected);

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
        $sentenceInList = $this->SentencesList->addSentenceToList(12, 1, 7);
        $expected = array(
            'sentence_id' => 12,
            'sentences_list_id' => 1
        );
        $result = array_intersect_key($sentenceInList['SentencesSentencesLists'], $expected);

        $this->assertEquals($expected, $result);
    }

    function testAddSentenceToList_failsBecauseUserNotAllowed() {
        $result = $this->SentencesList->addSentenceToList(4, 1, 1);
        
        $this->assertEmpty($result);
    }

    function testAddSentenceToList_failsBecauseSentenceAlreadyInList() {
        $result = $this->SentencesList->addSentenceToList(4, 1, 7);

        $this->assertEmpty($result);
    }

    function testAddSentenceToList_failsBecauseUnknownSentenceId() {
        $result = $this->SentencesList->addSentenceToList(10000, 1, 7);

        $this->assertEmpty($result);
    }


    function testAddNewSentenceToList_succeeds() {
        $listId = 1;
        $lang = 'eng';
        $text = 'This is a new shiny sentence.';
        $userId = 7;
        $data = $this->SentencesList->addNewSentenceToList($listId, $text, $lang, $userId);
        
        $expectedSentence = array('lang' => $lang, 'text' => $text);
        $expectedUser = array('id' => $userId);
        $expected = array(
            'Sentence' => $expectedSentence,
            'User' => $expectedUser
        );
        $result = array(
            'Sentence' => array_intersect_key($data['Sentence'], $expectedSentence),
            'User' => array_intersect_key($data['User'], $expectedUser)
        );
        
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
            ->where(['sentence_id' => $sentenceId])
            ->count();
        $this->SentencesList->removeSentenceFromList($sentenceId, $listId, $userId);
        $after = $this->SentencesList->SentencesSentencesLists->find()
            ->where(['sentence_id' => $sentenceId])
            ->count();
        $this->assertEquals(1, $before - $after);
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
        $result = $this->SentencesList->removeSentenceFromList(4, 2, 7);

        $this->assertFalse($result);
    }

    function testGetUserChoices() {
        CurrentUser::store(['id' => 7]);
        $result = $this->SentencesList->getUserChoices(7);
        $expected = [
            'OfUser' => ['1' => 'Interesting French sentences'],
            'Collaborative' => []
        ];
        $this->assertEquals($expected, $result);
    }

    function testGetSearchableLists_asGuests() {
        $result = $this->SentencesList->getSearchableLists();
        $this->assertEquals([2], Hash::extract($result, '{n}.id'));
    }

    function testGetSearchableLists_asMember() {
        CurrentUser::store(['id' => 7]);
        $result = $this->SentencesList->getSearchableLists();
        $this->assertEquals([1, 2], Hash::extract($result, '{n}.id'));
    }

    function testGetNameForListWithId() {
        $result = $this->SentencesList->getNameForListWithId(1);
        $this->assertEquals('Interesting French sentences', $result);
    }

    function testGetNumberOfSentences() {
        $result = $this->SentencesList->getNumberOfSentences(1);
        $this->assertEquals(2, $result);
    }
}
