<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PrivateMessagesTable;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Event\Event;
use App\Model\CurrentUser;

class PrivateMessageTest extends TestCase {

    public $fixtures = array(
        'app.private_messages',
        'app.users',
        'app.users_languages'
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
        $userId = 7;
        CurrentUser::store(['id' => $userId]);
        $date = '1999-12-31 23:59:59';
        $postData = array(
            'recipients' => 'advanced_contributor',
            'title' => 'Status',
            'content' => 'Why are you so advanced?',
            'messageId' => '',
            'submitType' => 'saveDraft',
        );

        $draft = $this->PrivateMessage->saveDraft($userId, $date, $postData);

        $id = $draft->id;
        $expectedPm = array(
            'id'      => $id,
            'recpt'   => 0,
            'sender'  => $userId,
            'user_id' => $userId,
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
        CurrentUser::store(['id' => 4]);
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
        $userId = 7;
        CurrentUser::store(['id' => $userId]);
        $date = '1999-12-31 23:59:59';
        $postData = array(
            'recipients' => '',
            'title' => 'Status',
            'content' => 'Why are you so advanced?',
            'messageId' => '',
            'submitType' => 'saveDraft',
        );

        $before = $this->PrivateMessage->find()->count();
        $this->PrivateMessage->saveDraft($userId, $date, $postData);
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
        $recpt = 3;
        CurrentUser::store(['id' => $currentUserId]);

        $message = $this->PrivateMessage->send($currentUserId, $date, $postData);

        $sentId = $this->PrivateMessage->find()->order(['id' => 'DESC'])->first()->id;
        $expectedSent = array(
            'id'      => $sentId,
            'recpt'   => $recpt,
            'sender'  => $currentUserId,
            'user_id' => $currentUserId,
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
            'recpt'   => $recpt,
            'sender'  => $currentUserId,
            'user_id' => $recpt,
            'date'    => $date,
            'folder'  => 'Inbox',
            'title'   => 'Status',
            'content' => 'Why are you so advanced?',
            'sent'    => 1,
            'isnonread' => 1,
            'draft_recpts' => '',
        );
        CurrentUser::store(['id' => $recpt]);
        $received = $this->PrivateMessage->get($receivedId)->old_format;
        $this->assertEquals($expectedReceived, $received['PrivateMessage']);
    }

    public function testSend_limitExceeded() {
        $date = date('Y-m-d H:i:s');
        $postData = array(
            'recipients' => 'advanced_contributor',
            'title' => 'Spamming',
            'content' => 'Spamming very much.',
            'messageId' => '',
            'submitType' => 'send',
        );
        $currentUserId = 9;
        CurrentUser::store(['id' => $currentUserId]);

        for ($i = 0; $i <= 5; $i++) {
            $message = $this->PrivateMessage->send($currentUserId, $date, $postData);
        }

        $this->assertFalse($message[0]);
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

    public function testDeleteMessage_movedToTrash()
    {
        CurrentUser::store(['id' => 3]);
        $this->PrivateMessage->deleteMessage(1);
        $pm = $this->PrivateMessage->get(1);
        $this->assertEquals('Trash', $pm->folder);
    }

    public function testDeleteMessage_deletedFromDatabase()
    {
        CurrentUser::store(['id' => 4]);
        $result = $this->PrivateMessage->deleteMessage(4);
        $pm = $this->PrivateMessage->findById(4)->first();
        $this->assertTrue($result);
        $this->assertNull($pm);
    }

    public function testDeleteMessage_failsBecauseNotAllowedToDelete()
    {
        CurrentUser::store(['id' => 1]);
        $deleted = $this->PrivateMessage->deleteMessage(1);
        $this->assertFalse($deleted);
    }

    public function testRestoreMessage_succeeds()
    {
        CurrentUser::store(['id' => 4]);
        $pm = $this->PrivateMessage->restoreMessage(4);
        $this->assertEquals('Sent', $pm->folder);
        $pm = $this->PrivateMessage->restoreMessage(7);
        $this->assertEquals('Drafts', $pm->folder);
        $pm = $this->PrivateMessage->restoreMessage(8);
        $this->assertEquals('Inbox', $pm->folder);
    }

    public function testRestoreMessage_fails()
    {
        CurrentUser::store(['id' => 1]);
        $pm = $this->PrivateMessage->restoreMessage(4);
        $this->assertFalse($pm);
    }

    public function testReadMessage_succeeds()
    {
        CurrentUser::store(['id' => 3]);
        $pm = $this->PrivateMessage->readMessage(1);
        $expectedContent = 'There is a problem with sentence #123.';
        $this->assertEquals($expectedContent, $pm->content);
    }

    public function testReadMessage_marksAsRead()
    {
        $userId = 3;
        CurrentUser::store(['id' => $userId]);
        $before = $this->PrivateMessage->find()
            ->where(['user_id' => $userId, 'isnonread' => 1])
            ->count();
        $pm = $this->PrivateMessage->readMessage(1);
        $after = $this->PrivateMessage->find()
            ->where(['user_id' => $userId, 'isnonread' => 1])
            ->count();
        $this->assertEquals(1, $before - $after);
    }

    public function testReadMessage_failsBecauseNotAllowed()
    {
        CurrentUser::store(['id' => 1]);
        $pm = $this->PrivateMessage->readMessage(1);
        $this->assertNull($pm);
    }

    public function testToggleUnread_marksAsRead()
    {
        $userId = 3;
        CurrentUser::store(['id' => $userId]);
        $before = $this->PrivateMessage->find()
            ->where(['user_id' => $userId, 'isnonread' => 1])
            ->count();
        $pm = $this->PrivateMessage->toggleUnread(1);
        $after = $this->PrivateMessage->find()
            ->where(['user_id' => $userId, 'isnonread' => 1])
            ->count();
        $this->assertEquals(1, $before - $after);
    }

    public function testToggleUnread_marksAsUnread()
    {
        $userId = 4;
        CurrentUser::store(['id' => $userId]);
        $before = $this->PrivateMessage->find()
            ->where(['user_id' => $userId, 'isnonread' => 1])
            ->count();
        $pm = $this->PrivateMessage->toggleUnread(2);
        $after = $this->PrivateMessage->find()
            ->where(['user_id' => $userId, 'isnonread' => 1])
            ->count();
        $this->assertEquals(1, $after - $before);
    }

    public function testToggleUnread_failsBecauseNotAllowed()
    {
        $userId = 3;
        CurrentUser::store(['id' => $userId]);
        $before = $this->PrivateMessage->find()
            ->where(['user_id' => $userId, 'isnonread' => 1])
            ->count();
        $pm = $this->PrivateMessage->toggleUnread(2);
        $after = $this->PrivateMessage->find()
            ->where(['user_id' => $userId, 'isnonread' => 1])
            ->count();
        $this->assertEquals($before, $after);
    }
}
