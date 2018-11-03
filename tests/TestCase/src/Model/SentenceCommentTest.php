<?php
namespace App\Test\TestCase\Model;

App::uses('SentenceComment', 'Model');

class SentenceCommentTest extends CakeTestCase {

    public $fixtures = array(
        'app.sentence_comment',
    );

    public function setUp() {
        parent::setUp();
        $this->SentenceComment = ClassRegistry::init('SentenceComment');
    }

    public function tearDown() {
        unset($this->SentenceComment);

        parent::tearDown();
    }

    public function testSave_newCommentfiresEvent() {
        $comment = array(
            'sentence_id' => 1,
            'text' => 'What a great sentence!',
            'user_id' => 4,
        );

        $dispatched = false;
        $model = $this->SentenceComment;
        $model->getEventManager()->attach(
            function (CakeEvent $event) use ($model, &$dispatched, $comment) {
                $this->assertSame($model, $event->subject());
                unset($event->data['comment']['id']);
                unset($event->data['comment']['modified']);
                unset($event->data['comment']['created']);
                $this->assertEquals($comment, $event->data['comment']);
                $dispatched = true;
            },
            'Model.SentenceComment.commentPosted'
        );

        $this->SentenceComment->save($comment);

        $this->assertTrue($dispatched);
    }

    public function testSave_savesNewComment() {
        $comment = array(
            'sentence_id' => 1,
            'text' => 'What a great sentence!',
            'user_id' => 4,
        );

        $saved = $this->SentenceComment->save($comment);

        $this->assertTrue((bool)$saved);
    }

    public function testSave_failsSavingNewEmptyComment() {
        $comment = array(
            'sentence_id' => 1,
            'text' => ' ',
            'user_id' => 4,
        );

        $saved = $this->SentenceComment->save($comment);

        $this->assertFalse((bool)$saved);
    }

    public function testSave_editsExistingComment() {
        $newText = 'Someone should REALLY delete this sentence.';

        $this->SentenceComment->id = 2;
        $saved = $this->SentenceComment->saveField('text', $newText, true);

        $this->assertTrue((bool)$saved);
    }

    public function testSave_failsEditingCommentWithEmptyContents() {
        $newText = ' ';

        $this->SentenceComment->id = 2;
        $saved = $this->SentenceComment->saveField('text', $newText, true);

        $this->assertFalse((bool)$saved);
    }
}
