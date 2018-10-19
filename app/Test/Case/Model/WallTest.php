<?php
App::uses('Wall', 'Model');

class WallTest extends CakeTestCase {

    public $fixtures = array(
        'app.wall',
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
}
