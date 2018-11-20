<?php
namespace App\Test\TestCase\Model;

use App\Model\Wall;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Event\Event;

class WallTest extends TestCase {

    public $fixtures = array(
        'app.walls',
        'app.wall_threads',
        'app.users'
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
        $rootId = $this->Wall->getRootMessageIdOfReply($postId);
        $wallThread = $this->Wall->WallThreads->get($rootId);
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
}
