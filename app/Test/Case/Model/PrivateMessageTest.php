<?php
App::uses('PrivateMessage', 'Model');

class PrivateMessageTest extends CakeTestCase {

    public $fixtures = array(
        'app.private_message',
    );

    public function setUp() {
        parent::setUp();
        $this->PrivateMessage = ClassRegistry::init('PrivateMessage');
    }

    public function tearDown() {
        unset($this->PrivateMessage);
        parent::tearDown();
    }

    public function testSaveDraft_addsNewDraft() {
        $date = '1999/12/31 23:59:59';
        $postData = array(
            'PrivateMessage' => array(
                'recpt' => 'advanced_contributor',
                'title' => 'Status',
                'content' => 'Why are you so advanced?',
                'messageId' => '',
                'submitType' => 'saveDraft',
            ),
        );

        $this->PrivateMessage->saveDraft(7, $date, $postData);

        $id = $this->PrivateMessage->getLastInsertId();
        $expectedPm = array(
            'id'      => $id,
            'recpt'   => 0,
            'sender'  => 7,
            'user_id' => 7,
            'date'    => '1999-12-31 23:59:59',
            'folder'  => 'Drafts',
            'title'   => 'Status',
            'content' => 'Why are you so advanced?',
            'sent'    => 0,
            'isnonread' => 1,
            'draft_recpts' => 'advanced_contributor',
        );
        $pm = $this->PrivateMessage->findById($id);
        $this->assertEqual($expectedPm, $pm['PrivateMessage']);
    }
}
