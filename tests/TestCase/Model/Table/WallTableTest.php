<?php
namespace App\Test\TestCase\Model;

use App\Model\CurrentUser;
use App\Model\Wall;
use Cake\Event\Event;
use Cake\Event\EventList;
use Cake\I18n\I18n;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class WallTest extends TestCase {

    public $fixtures = array(
        'app.walls',
        'app.wall_threads',
        'app.users',
        'app.users_languages',
    );

    public function setUp() {
        parent::setUp();
        $this->Wall = TableRegistry::getTableLocator()->get('Wall');
    }

    public function tearDown() {
        unset($this->Wall);

        parent::tearDown();
    }

    public function testSave_createsPost() {
        $newPost = $this->Wall->newEntity([
            'owner' => 2,
            'content' => 'Hi everyone!',
        ]);
        $expected = [
            'owner' => 2,
            'content' => 'Hi everyone!',
            'parent_id' => null,
            'lft' => 7,
            'rght' => 8,
            'id' => 4,
        ];

        $before = $this->Wall->find()->count();
        $saved = $this->Wall->save($newPost);
        $result = $saved->extract(
            ['owner', 'content', 'parent_id', 'lft', 'rght', 'id']
        );
        $after = $this->Wall->find()->count();

        $this->assertEquals(1, $after - $before);
        $this->assertEquals($expected, $result);
    }

    public function testSave_setsDates() {
        $newPost = $this->Wall->newEntity([
            'owner' => 2,
            'content' => 'Hi everyone!',
        ]);
        $saved = $this->Wall->save($newPost);
        
        $this->assertNotNull($saved->date);
        $this->assertEquals($saved->date->format('Y-m-d H:i:s'), $saved->modified->format('Y-m-d H:i:s'));
    }

    public function testSave_doesNotSaveNewPostIfContentIsEmpty() {
        $newPost = $this->Wall->newEntity([
            'owner' => 2,
            'date' => '2018-01-02 03:04:05',
            'content' => '       ',
        ]);

        $before = $this->Wall->find()->count();
        $saved = $this->Wall->save($newPost);
        $after = $this->Wall->find()->count();

        $this->assertEquals(0, $after - $before);
        $this->assertFalse($saved);
    }

    public function testSave_doesNotSaveExistingPostIfContentIsEmpty() {
        $post = $this->Wall->get(2);
        $this->Wall->patchEntity($post, ['content' => '']);

        $saved = $this->Wall->save($post);

        $this->assertFalse($saved);
    }

    private function _assertThreadDate($postId, $expectedDate) {
        $root = $this->Wall->getRootMessageOfReply($postId);
        $wallThread = $this->Wall->WallThreads->get($root->id);
        $threadDate = $wallThread->last_message_date;
        $this->assertEquals($expectedDate, $threadDate);
    }

    public function testSave_newPostUpdatesExistingThreadDate() {
        $date = new Time('2018-01-02 03:04:05');
        $reply = $this->Wall->newEntity([
            'owner' => 7,
            'date' => $date,
            'parent_id' => 2,
            'content' => 'I see.',
        ]);

        $saved = $this->Wall->save($reply);
        
        $postId = $saved->id;
        $this->_assertThreadDate($postId, $date);
    }

    public function testSave_newPostUpdatesNewThreadDate() {
        $date = new Time('2018-01-02 03:04:05');
        $newPost = $this->Wall->newEntity([
            'owner' => 2,
            'date' => $date,
            'content' => 'Hi everyone!',
        ]);

        $saved = $this->Wall->save($newPost);

        $postId = $saved->id;
        $this->_assertThreadDate($postId, $date);
    }

    public function testSave_editExistingPostDoesNotUpdateThreadDate() {
        $postId = 2;
        $post = $this->Wall->get($postId);
        $this->Wall->patchEntity($post, [
            'content' => 'Today!',
        ]);

        $this->Wall->save($post);

        $this->_assertThreadDate($postId, new Time('2014-04-15 16:38:36'));
    }

    public function testSave_editExistingPostUpdatesModifiedDate() {
        $postId = 2;
        $post = $this->Wall->get($postId);
        $oldModified = $post->modified;
        $this->Wall->patchEntity($post, [
            'content' => 'Today!',
        ]);

        $newPost = $this->Wall->save($post);

        $this->assertLessThan($newPost->modified, $oldModified);
    }

    public function testSave_hidingMessageDoesNotUpdateLastModifiedField() {
        $postId = 2;
        $post = $this->Wall->get($postId);
        $before = $post->modified;
        $post->hidden = true;
        $this->Wall->save($post);
        $after = $this->Wall->get($postId, ['fields' => ['modified']])->modified;

        $this->assertEquals($before, $after);
    }

    public function testSave_doesNotFireEvent() {
        $eventManager = $this->Wall->getEventManager();
        $eventManager->setEventList(new EventList());
        $post = $this->Wall->newEntity([
            'owner' => 7,
            'content' => 'new post',
        ]);
        $this->Wall->save($post);
        $eventList = $eventManager->getEventList();
        $this->assertFalse($eventList->hasEvent('Model.Wall.replyPosted'));
    }

    public function testSaveReply_firesEvent() {
        $expectedPost = array(
            'owner' => 7,
            'parent_id' => 2,
            'content' => 'I see.',
            'lft' => 3,
            'rght' => 4,
        );
        $dispatched = false;
        $model = $this->Wall;
        $model->getEventManager()->on(
            'Model.Wall.replyPosted',
            function (Event $event) use ($model, &$dispatched, $expectedPost) {
                $this->assertSame($model, $event->getSubject());
                $post = $event->getData('post')->extract(
                    ['owner', 'parent_id', 'content', 'lft', 'rght']
                ); // $post
                $this->assertEquals($expectedPost, $post);
                $dispatched = true;
            }
        );

        $this->Wall->saveReply(2, 'I see.', 7);

        $this->assertTrue($dispatched);
    }

    public function testGetMessagesThreaded() {
        $rootMessages = $this->Wall->find()
            ->where(['parent_id IS NULL'])
            ->all();
        $threads = $this->Wall->getMessagesThreaded($rootMessages);
        $this->assertEquals(2, count($threads));
        $this->assertEquals(0, count($threads[0]->children));
        $this->assertEquals(1, count($threads[1]->children));
    }

    public function testGetMessagesThreaded_empty() {
        $this->Wall->deleteAll([]);
        $rootMessages = $this->Wall->find()->all();
        $threads = $this->Wall->getMessagesThreaded($rootMessages);
        $this->assertCount(0, $threads);
    }

    public function testDeleteMessage_succeeds() {
        CurrentUser::store(['id' => 1]);
        $result = $this->Wall->deleteMessage(2);
        $this->assertTrue($result);
    }

    public function testDeleteMessage_failsWrongId() {
        CurrentUser::store(['id' => 1]);
        $result = $this->Wall->deleteMessage(99999);
        $this->assertFalse($result);
    }

    public function testDeleteMessage_failsBecauseHasReplies() {
        CurrentUser::store(['id' => 1]);
        $result = $this->Wall->deleteMessage(1);
        $this->assertFalse($result);
    }

    public function testDeleteMessage_failsBecauseUserDoesntHavePermission() {
        CurrentUser::store(['id' => 7]);
        $result = $this->Wall->deleteMessage(2);
        $this->assertFalse($result);
    }

    public function testSaveReply_succeeds() {
        $content = 'I hope soon.';
        $result = $this->Wall->saveReply(2, $content, 7);
        $this->assertEquals(2, $result->parent_id);
    }

    public function testSaveReply_failsBecauseNoParentId() {
        $content = 'I hope soon.';
        $result = $this->Wall->saveReply(null, $content, 7);
        $this->assertNull($result);
    }

    public function testSaveReply_failsBecauseEmptyContent() {
        $content = '   ';
        $result = $this->Wall->saveReply(2, $content, 7);
        $this->assertNull($result);
    }

    public function testGetRootMessageOfReply() {
        $expected = ['id' => 1, 'lft' => 1, 'rght' => 4];
        $result = $this->Wall->getRootMessageOfReply(2)->toArray();
        $this->assertEquals($expected, $result);
    }

    public function testGetWholeThreadContaining_succeeds() {
        $result = $this->Wall->getWholeThreadContaining(2);
        $this->assertEquals(1, count($result[0]->children));
    }

    public function testGetWholeThreadContaining_failsBecauseWrongId() {
        $result = $this->Wall->getWholeThreadContaining(999999);
        $this->assertEquals([], $result);
    }

    public function testSave_correctDateUsingArabicLocale() {
        $prevLocale = I18n::getLocale();
        I18n::setLocale('ar');

        $post = $this->Wall->newEntity(['content' => 'test', 'owner' => 1]);
        $added = $this->Wall->save($post);
        $returned = $this->Wall->get($added->id);
        $this->assertEquals($added->date->format('Y-m-d H:i:s'), $returned->date->format('Y-m-d H:i:s'));
        $this->assertEquals($added->modified->format('Y-m-d H:i:s'), $returned->modified->format('Y-m-d H:i:s'));

        I18n::setLocale($prevLocale);
    }

    public function testHidingAMessageRecalculatesThreadDateIgnoringHiddenPosts() {
        $reply = $this->Wall->get(2);
        $reply->hidden = true;
        $this->Wall->save($reply);

        $threadDate = $this->Wall->WallThreads->get(1)->last_message_date;
        $this->assertEquals('2014-04-15 16:37:11', $threadDate);
    }
}
