<?php
namespace App\Test\TestCase\Model;

use App\Model\Wall;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Event\Event;
use App\Model\CurrentUser;

class WallTest extends TestCase {

    public $fixtures = array(
        'app.walls',
        'app.wall_threads',
        'app.users',
        'app.users_languages',
    );

    public function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
        $this->Wall = TableRegistry::getTableLocator()->get('Wall');
    }

    public function tearDown() {
        unset($this->Wall);

        parent::tearDown();
    }

    public function testSave_createsPost() {
        $newPost = $this->Wall->newEntity([
            'owner' => 2,
            'date' => '2018-01-02 03:04:05',
            'content' => 'Hi everyone!',
        ]);
        $expected = array('Wall' => array(
            'owner' => 2,
            'date' => '2018-01-02 03:04:05',
            'content' => 'Hi everyone!',
            'modified' => '2018-01-02 03:04:05',
            'parent_id' => null,
            'lft' => 5,
            'rght' => 6,
            'id' => 3,
        ));

        $before = $this->Wall->find()->count();
        $saved = $this->Wall->save($newPost)->old_format;
        $after = $this->Wall->find()->count();

        $this->assertEquals(1, $after - $before);
        $this->assertEquals($expected, $saved);
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
        $date = '2018-01-02 03:04:05';
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
        $date = '2018-01-02 03:04:05';
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
        $post = $this->Wall->newEntity([
            'id' => $postId,
            'content' => 'Today!',
        ]);

        $this->Wall->save($post);

        $this->_assertThreadDate($postId, '2014-04-15 16:38:36');
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

    public function testSave_replyFiresEvent() {
        $date = '2018-01-02 03:04:05';
        $reply = $this->Wall->newEntity([
            'owner' => 7,
            'date' => $date,
            'parent_id' => 2,
            'content' => 'I see.',
        ]);
        $expectedPost = array(
            'owner' => 7,
            'date' => '2018-01-02 03:04:05',
            'modified' => '2018-01-02 03:04:05',
            'parent_id' => 2,
            'content' => 'I see.',
            'lft' => 3,
            'rght' => 4,
        );
        $dispatched = false;
        $model = $this->Wall;
        $model->getEventManager()->on(
            'Model.Wall.postPosted',
            function (Event $event) use ($model, &$dispatched, $expectedPost) {
                $this->assertSame($model, $event->getSubject());
                $post = $event->getData('post')->old_format['Wall']; // $post
                unset($post['id']);
                $this->assertEquals($expectedPost, $post);
                $dispatched = true;
            }
        );

        $saved = $this->Wall->save($reply);

        $this->assertTrue($dispatched);
    }

    public function testGetMessagesThreaded() {
        $rootMessages = $this->Wall->find()
            ->where(['parent_id IS NULL'])
            ->toList();
        $threads = $this->Wall->getMessagesThreaded($rootMessages);
        $this->assertEquals(1, count($threads[0]->children));
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
        $this->assertEquals(3, $result->id);
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
}
