<?php
App::uses('Wall', 'Model');

class WallTest extends CakeTestCase {

    public $fixtures = array(
        'app.wall',
        'app.wall_thread',
        'app.user',
    );

    public function setUp() {
        parent::setUp();
        $this->Wall = ClassRegistry::init('Wall');
    }

    public function tearDown() {
        unset($this->Wall);

        parent::tearDown();
    }

    public function testSave_createsPost() {
        $newPost = array(
            'owner' => 2,
            'date' => '2018-01-02 03:04:05',
            'content' => 'Hi everyone!',
        );
        $expected = array('Wall' => array(
            'owner' => 2,
            'date' => '2018-01-02 03:04:05',
            'content' => 'Hi everyone!',
            'modified' => '2018-01-02 03:04:05',
            'lft' => 5,
            'rght' => 6,
            'id' => 3,
        ));

        $before = $this->Wall->find('count');
        $saved = $this->Wall->save($newPost);
        $after = $this->Wall->find('count');

        $this->assertEquals(1, $after - $before);
        $this->assertEquals($expected, $saved);
    }

    private function _assertThreadDate($postId, $expectedDate) {
        $rootId = $this->Wall->getRootMessageIdOfReply($postId);
        $wallThread = $this->Wall->WallThread->findById($rootId, 'last_message_date');
        $threadDate = $wallThread[$this->Wall->WallThread->alias]['last_message_date'];
        $this->assertEquals($expectedDate, $threadDate);
    }

    public function testSave_newPostUpdatesExistingThreadDate() {
        $date = '2018-01-02 03:04:05';
        $reply = array(
            'owner' => 7,
            'date' => $date,
            'parent_id' => 2,
            'content' => 'I see.',
        );

        $saved = $this->Wall->save($reply);

        $postId = $saved[$this->Wall->alias][$this->Wall->primaryKey];
        $this->_assertThreadDate($postId, $date);
    }

    public function testSave_newPostUpdatesNewThreadDate() {
        $date = '2018-01-02 03:04:05';
        $newPost = array(
            'owner' => 2,
            'date' => $date,
            'content' => 'Hi everyone!',
        );

        $saved = $this->Wall->save($newPost);

        $postId = $saved[$this->Wall->alias][$this->Wall->primaryKey];
        $this->_assertThreadDate($postId, $date);
    }

    public function testSave_editExistingPostDoesNotUpdateThreadDate() {
        $postId = 2;
        $post = array(
            'id' => $postId,
            'content' => 'Today!',
        );

        $this->Wall->save($post);

        $this->_assertThreadDate($postId, '2014-04-15 16:38:36');
    }
}
