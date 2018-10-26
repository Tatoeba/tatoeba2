<?php
App::uses('SentencesList', 'Model');
App::uses('Sanitize', 'Utility');
class SentencesListTest extends CakeTestCase {
    public $fixtures = array(
        'app.sentences_list',
        'app.sentence',
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
}
