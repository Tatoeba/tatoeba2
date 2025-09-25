<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\CurrentUser;
use App\Model\Table\SentenceCommentsTable;
use Cake\Event\EventList;
use Cake\Http\ServerRequest;
use Cake\I18n\I18n;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
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

        Router::pushRequest(new ServerRequest([
            'environment' => [
                'HTTP_HOST' => 'tatoeba.org',
                'HTTPS' => 'on',
            ],
        ]));
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

    public function newCommentsProvider() {
        return [
            // user id, validator, comment, should be able to save
            'legacy user, inbound link'        => [1, 'default', 'Hi! https://tatoeba.org/en/sentences_lists/show/1234', true],
            'legacy user, outbound link'       => [1, 'default', 'Hi! https://example.com', true],
            'verified user, inbound link'      => [7, 'default', 'Hi! https://tatoeba.org/en/sentences_lists/show/1234', true],
            'verified user, outbound link'     => [7, 'default', 'Hi! https://example.com', true],
            'new user, inbound link'           => [9, 'default', 'Hi! https://tatoeba.org/en/sentences_lists/show/1234', true],
            'new user, outbound link'          => [9, 'default', 'Hi! https://example.com', false],
            'new user, outbound link, confirm' => [9, 'skipOutboundLinksCheck', 'Hi! https://example.com', true],
        ];
    }

    /**
     * @dataProvider newCommentsProvider()
     */
    public function testAddCommentWithLinks($userId, $validator, $text, $expectedToSave)
    {
        CurrentUser::store($this->SentenceComment->Users->get($userId));
        $comment = $this->SentenceComment->newEntity(
            [
                'sentence_id' => 1,
                'text' => $text,
                'user_id' => $userId,
            ],
            ['validate' => $validator]
        );

        $savedComment = $this->SentenceComment->save($comment);
        if ($expectedToSave) {
            $this->assertNotFalse($savedComment);
            $this->assertEquals($text, $savedComment->text);
            $this->assertEquals($userId, $savedComment->user_id);
        } else {
            $this->assertFalse($savedComment);
        }
    }

    /**
     * @dataProvider newCommentsProvider()
     */
    public function testEditCommentWithLinks($userId, $validate, $text, $expectedToSave)
    {
        CurrentUser::store($this->SentenceComment->Users->get($userId));
        $comment = $this->SentenceComment->get(6);
        $this->SentenceComment->patchEntity(
            $comment,
            compact('text'),
            compact('validate')
        );

        $savedComment = $this->SentenceComment->save($comment);
        if ($expectedToSave) {
            $this->assertNotFalse($savedComment);
            $this->assertEquals($text, $savedComment->text);
        } else {
            $this->assertFalse($savedComment);
        }
    }
}
