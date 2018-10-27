<?php
App::uses('SentencesList', 'Model');
App::uses('Sanitize', 'Utility');
class SentencesListTest extends CakeTestCase {
    public $fixtures = array(
        'app.sentences_list',
        'app.sentences_sentences_list',
        'app.sentence',
        'app.favorites_user',
        'app.user'
    );

    function setUp() {
        parent::setUp();
        $this->SentencesList = ClassRegistry::init('SentencesList');
    }

    function tearDown() {
        unset($this->SentencesList);
        parent::tearDown();
    }

    function testGetListWithPermissions() {
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

        $this->assertEquals(false, $list);
    }

    function testDeleteList_succeeds() {
        $list = $this->SentencesList->deleteList(1, 7);

        $this->assertEquals(true, $list);
    }

    function testDeleteList_fails() {
        $list = $this->SentencesList->deleteList(1, 1);

        $this->assertEquals(false, $list);
    }

    function testEditName_suceeds() {
        $listId = 1;
        $newName = 'Very interesting French sentences';    
        $date = new DateTime();
        $list = $this->SentencesList->editName($listId, $newName, 7);
        
        $expected = array(
            'id' => $listId,
            'name' => $newName,
            'modified' => $date->format('Y-m-d H:i:s')
        );

        $this->assertEquals($list['SentencesList'], $expected);
    }

    function testEditName_fails() {
        $list = $this->SentencesList->editName(1, 'x', 1);

        $this->assertEquals(false, $list);
    }

    function testEditOption_succeeds() {
        $date = new DateTime();
        $list = $this->SentencesList->editOption(1, 'editable_by', 'everyone', 7);
        $expected = array(
            'id' => 1,
            'editable_by' => 'everyone',
            'modified' => $date->format('Y-m-d H:i:s')
        );
        $this->assertEquals($list['SentencesList'], $expected);
    }

    function testEditOption_fails() {
        $list = $this->SentencesList->editOption(1, 'editable', 'everyone', 7);
        $this->assertEquals(array(), $list);
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

    function testAddSentenceToList_fails() {
        $result1 = $this->SentencesList->addSentenceToList(4, 1, 7);
        $result2 = $this->SentencesList->addSentenceToList(10000, 1, 7);
        $result3 = $this->SentencesList->addSentenceToList(4, 1, 1);

        $result = array(
            'result1' => $result1,
            'result2' => $result2,
            'result3' => $result3
        );
        $expected = array(
            'result1' => array(),
            'result2' => array(),
            'result3' => array()
        );

        $this->assertEquals($expected, $result);
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

    function testAddNewSentenceToList_fails() {
        $result1 = $this->SentencesList->addNewSentenceToList(1, 'x', 'eng', 1);
        $result2 = $this->SentencesList->addNewSentenceToList(1, '', 'eng', 7);

        $result = array(
            'result1' => $result1,
            'result2' => $result2
        );
        $expected = array(
            'result1' => false,
            'result2' => false
        );

        $this->assertEquals($expected, $result);
    }

    function testRemoveSentenceFromList() {
        $sentenceId = 4;
        $listId = 1;
        $userId = 7;
        $before = $this->SentencesList->SentencesSentencesLists->find('count',
            array('conditions' => array(
                'sentence_id' => $sentenceId,
            )
        ));
        $this->SentencesList->removeSentenceFromList($sentenceId, $listId, $userId);
        $after = $this->SentencesList->SentencesSentencesLists->find('count',
            array('conditions' => array(
                'sentence_id' => $sentenceId,
            )
        ));
        $this->assertEqual(1, $before - $after);
    }

    function testRemoveSentenceFromList_succeeds() {
        $result = $this->SentencesList->removeSentenceFromList(4, 1, 7);
        $this->assertEquals(true, $result);
    }

    function testRemoveSentenceFromList_fails() {
        $result1 = $this->SentencesList->removeSentenceFromList(10000, 1, 7);
        $result2 = $this->SentencesList->removeSentenceFromList(4, 1, 1);
        $result3 = $this->SentencesList->removeSentenceFromList(4, 2, 7);

        $result = array(
            'result1' => $result1,
            'result2' => $result2,
            'result3' => $result3
        );
        $expected = array(
            'result1' => false,
            'result2' => false,
            'result3' => false
        );

        $this->assertEquals($expected, $result);
    }
}
