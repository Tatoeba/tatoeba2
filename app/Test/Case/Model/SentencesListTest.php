<?php
App::uses('SentencesList', 'Model');

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
}
