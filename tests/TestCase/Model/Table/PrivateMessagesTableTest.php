<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PrivateMessagesTable;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Event\Event;

class PrivateMessageTest extends TestCase {

    public $fixtures = array(
        'app.private_messages',
        'app.users'
    );

    public function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
        $this->PrivateMessage = TableRegistry::getTableLocator()->get('PrivateMessages');
    }

    public function tearDown() {
        unset($this->PrivateMessage);
        parent::tearDown();
    }

    public function testSaveDraft_addsNewDraft() {
        $date = '1999-12-31 23:59:59';
        $postData = array(
            'recipients' => 'advanced_contributor',
            'title' => 'Status',
            'content' => 'Why are you so advanced?',
            'messageId' => '',
            'submitType' => 'saveDraft',
        );

        $draft =$this->PrivateMessage->saveDraft(7, $date, $postData);

        $id = $draft->id;
        $expectedPm = array(
            'id'      => $id,
            'recpt'   => 0,
            'sender'  => 7,
            'user_id' => 7,
            'date'    => $date,
            'folder'  => 'Drafts',
            'title'   => 'Status',
            'content' => 'Why are you so advanced?',
            'sent'    => 0,
            'isnonread' => 1,
            'draft_recpts' => 'advanced_contributor',
        );
        $pm = $this->PrivateMessage->get($id)->old_format;
        $this->assertEquals($expectedPm, $pm['PrivateMessage']);
    }

    public function testSaveDraft_editsExistingDraft() {
        $draftId = 5;
        $date = '2017-10-13 01:07:10';
        $postData = array(
            'recipients' => 'advanced_contributor',
            'title' => 'My feelings',
            'content' => 'I\'m worrying about Tom. What do you think?',
            'messageId' => $draftId,
            'submitType' => 'saveDraft',
        );

        $this->PrivateMessage->saveDraft(4, $date, $postData);

        $expectedPm = array(
            'id'      => $draftId,
            'recpt'   => 0,
            'sender'  => 4,
            'user_id' => 4,
            'date'    => $date,
            'folder'  => 'Drafts',
            'title'   => 'My feelings',
            'content' => 'I\'m worrying about Tom. What do you think?',
            'sent'    => 0,
            'isnonread' => 1,
            'draft_recpts' => 'advanced_contributor',
        );
        $pm = $this->PrivateMessage->get($draftId)->old_format;
        $this->assertEquals($expectedPm, $pm['PrivateMessage']);
    }

    public function testSave_failsIfEmptyContent() {
        $pm = $this->PrivateMessage->newEntity([
            'recpt'        => 3,
            'sender'       => 1,
            'user_id'      => 3,
            'date'         => '1999-12-31 23:59:59',
            'folder'       => 'Inbox',
            'title'        => 'Hello',
            'content'      => '',
            'isnonread'    => 1,
            'draft_recpts' => '',
            'sent'         => 0,
        ]);

        $before = $this->PrivateMessage->find()->count();
        $this->PrivateMessage->save($pm);
        $after = $this->PrivateMessage->find()->count();

        $this->assertEquals(0, $after - $before);
    }

    public function testSaveDraft_withoutRecipient() {
        $date = '1999-12-31 23:59:59';
        $postData = array(
            'recipients' => '',
            'title' => 'Status',
            'content' => 'Why are you so advanced?',
            'messageId' => '',
            'submitType' => 'saveDraft',
        );

        $before = $this->PrivateMessage->find()->count();
        $this->PrivateMessage->saveDraft(7, $date, $postData);
        $after = $this->PrivateMessage->find()->count();

        $this->assertEquals(1, $after - $before);
    }

    public function testSend_toOneRecipent() {
        $date = '1999-12-31 23:59:59';
        $postData = array(
            'recipients' => 'advanced_contributor',
            'title' => 'Status',
            'content' => 'Why are you so advanced?',
            'messageId' => '',
            'submitType' => 'send',
        );
        $currentUserId = 7;

        $message = $this->PrivateMessage->send($currentUserId, $date, $postData);

        $sentId = $this->PrivateMessage->find()->order(['id' => 'DESC'])->first()->id;
        $expectedSent = array(
            'id'      => $sentId,
            'recpt'   => 3,
            'sender'  => 7,
            'user_id' => 7,
            'date'    => $date,
            'folder'  => 'Sent',
            'title'   => 'Status',
            'content' => 'Why are you so advanced?',
            'sent'    => 1,
            'isnonread' => 0,
            'draft_recpts' => '',
        );
        $sent = $this->PrivateMessage->get($sentId)->old_format;
        $this->assertEquals($expectedSent, $sent['PrivateMessage']);

        $receivedId = $sentId - 1;
        $expectedReceived = array(
            'id'      => $receivedId,
            'recpt'   => 3,
            'sender'  => 7,
            'user_id' => 3,
            'date'    => $date,
            'folder'  => 'Inbox',
            'title'   => 'Status',
            'content' => 'Why are you so advanced?',
            'sent'    => 1,
            'isnonread' => 1,
            'draft_recpts' => '',
        );
        $received = $this->PrivateMessage->get($receivedId)->old_format;
        $this->assertEquals($expectedReceived, $received['PrivateMessage']);
    }

    public function testSend_withoutRecipient() {
        $date = '1999-12-31 23:59:59';
        $postData = array(
            'recpt' => '',
            'title' => 'Status',
            'content' => 'Why are you so advanced?',
            'messageId' => '',
            'submitType' => 'send',
        );
        $currentUserId = 4;

        $before = $this->PrivateMessage->find()->count();
        $this->PrivateMessage->send($currentUserId, $date, $postData);
        $after = $this->PrivateMessage->find()->count();

        $this->assertEquals(0, $after - $before);
    }

    function testSend_firesSendingEvent() {
        $date = '1999-12-31 23:59:59';
        $postData = array(
            'recipients' => 'advanced_contributor',
            'title' => 'Status',
            'content' => 'Why are you so advanced?',
            'messageId' => '',
            'submitType' => 'send',
        );
        $currentUserId = 7;
        $expectedMessage = array(
            'recpt' => 3,
            'sender' => 7,
            'date' => '1999-12-31 23:59:59',
            'folder' => 'Inbox',
            'title' => 'Status',
            'content' => 'Why are you so advanced?',
            'isnonread' => 1,
            'user_id' => 3,
            'draft_recpts' => '',
            'sent' => 1,
        );

        $dispatched = false;
        $model = $this->PrivateMessage;
        $model->getEventManager()->on(
            'Model.PrivateMessage.messageSent',
            function (Event $event) use ($model, &$dispatched, $expectedMessage) {
                $this->assertSame($model, $event->getSubject());
                $message = $event->getData('message')->old_format['PrivateMessage']; // $message
                unset($message['id']);
                $this->assertEquals($expectedMessage, $message);
                $dispatched = true;
            }
        );

        $this->PrivateMessage->send($currentUserId, $date, $postData);

        $this->assertTrue($dispatched);
    }

}
