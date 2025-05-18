<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\CurrentUser;
use App\Model\Table\SentenceCommentsTable;
use Cake\Event\EventList;
use Cake\I18n\I18n;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class SentenceCommentTest extends TestCase {

    public $fixtures = array(
        'app.sentence_comments',
        'app.users_languages',
        'app.users',
        'app.sentences',
    );

    public function setUp() {
        parent::setUp();
        $this->SentenceComment = TableRegistry::getTableLocator()->get('SentenceComments');
    }

    public function tearDown() {
        unset($this->SentenceComment);

        parent::tearDown();
    }

    public function testSave_newCommentfiresEvent() {
        $eventManager = $this->SentenceComment->getEventManager();
        $eventManager->setEventList(new EventList());

        $comment = $this->SentenceComment->newEntity([
            'sentence_id' => 1,
            'text' => 'What a great sentence!',
            'user_id' => 4,
        ]);

        $saved = $this->SentenceComment->save($comment);

        $this->assertEventFiredWith(
            'Model.SentenceComment.commentPosted',
            'comment',
            $saved,
            $eventManager
        );
    }

    public function testSave_savesNewComment() {
        $comment = $this->SentenceComment->newEntity([
            'sentence_id' => 1,
            'text' => 'What a great sentence!',
            'user_id' => 4,
        ]);

        $saved = $this->SentenceComment->save($comment);

        $this->assertTrue((bool)$saved);
    }

    public function testSave_failsSavingNewEmptyComment() {
        $comment = $this->SentenceComment->newEntity([
            'sentence_id' => 1,
            'text' => ' ',
            'user_id' => 4,
        ]);

        $saved = $this->SentenceComment->save($comment);

        $this->assertFalse((bool)$saved);
    }

    public function testSave_editsExistingComment() {
        $comment = $this->SentenceComment->get(2);
        $this->SentenceComment->patchEntity(
            $comment,
            ['text' => 'Someone should REALLY delete this sentence.']
        );
        $saved = $this->SentenceComment->save($comment);

        $this->assertTrue((bool)$saved);
    }

    public function testSave_failsEditingCommentWithEmptyContents() {
        $comment = $this->SentenceComment->get(2);
        $this->SentenceComment->patchEntity($comment, ['text' => ' ']);
        $saved = $this->SentenceComment->save($comment);

        $this->assertFalse((bool)$saved);
    }

    public function testSave_hidingMessageDoesNotUpdateLastModifiedField() {
        $messageId = 2;
        $message = $this->SentenceComment->get($messageId);
        $before = $message->modified;
        $message->hidden = true;
        $this->SentenceComment->save($message);
        $after = $this->SentenceComment->get($messageId, ['fields' => ['modified']])->modified;

        $this->assertEquals($before, $after);
    }

    public function testDeleteComment_succeeds() {
        CurrentUser::store(['id' => 3]);
        $result = $this->SentenceComment->deleteComment(2);
        $this->assertTrue($result);
    }

    public function testDeleteComment_failsWrongId() {
        CurrentUser::store(['id' => 3]);
        $result = $this->SentenceComment->deleteComment(99999);
        $this->assertFalse($result);
    }

    public function testDeleteComment_failsBecauseNotAllowed() {
        CurrentUser::store(['id' => 3]);
        $result = $this->SentenceComment->deleteComment(1);
        $this->assertFalse($result);
    }

    public function testGetLatestComments_hasSentenceOwnerInfo() {
        $result = $this->SentenceComment->getLatestComments(1);
        $this->assertEquals('contributor', $result[0]->sentence->user->username);
    }

    public function testSave_correctDateUsingArabicLocale() {
        $prevLocale = I18n::getLocale();
        I18n::setLocale('ar');

        $comment = $this->SentenceComment->newEntity([
            'sentence_id' => 1,
            'text' => 'test',
            'user_id' => 1,
        ]);
        $added = $this->SentenceComment->save($comment);
        $returned = $this->SentenceComment->get($added->id);
        $this->assertEquals($added->created->format('Y-m-d H:i:s'), $returned->created->format('Y-m-d H:i:s'));
        $this->assertEquals($added->modified->format('Y-m-d H:i:s'), $returned->modified->format('Y-m-d H:i:s'));

        I18n::setLocale($prevLocale);
    }
}
