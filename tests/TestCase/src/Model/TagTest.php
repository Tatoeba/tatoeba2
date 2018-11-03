<?php
namespace App\Test\TestCase\Model;

use App\Model\Tag;
use Cake\TestSuite\TestCase;

class TagTest extends TestCase {
    public $fixtures = array(
        'app.sentences',
        'app.users',
        'app.groups',
        'app.sentence_comments',
        'app.contributions',
        'app.sentences_lists',
        'app.sentences_sentences_lists',
        'app.walls',
        'app.wall_threads',
        'app.favorites_users',
        'app.tags',
        'app.tags_sentences',
        'app.languages',
        'app.links',
        'app.sentence_annotations',
        'app.transcriptions',
        'app.reindex_flags'
    );

    function startTest($method) {
        $this->Tag = ClassRegistry::init('Tag');
    }

    function endTest($method) {
        unset($this->Tag);
        ClassRegistry::flush();
    }

    function testAddTagAddsTag() {
        $contributorId = 4;
        $sentenceId = 1;
        $before = $this->Tag->TagsSentences->find('count');

        $this->Tag->addTag('@needs_native_check', $contributorId, $sentenceId);

        $after = $this->Tag->TagsSentences->find('count');
        $added = $after - $before;
        $this->assertEqual(1, $added);
    }

    function testAddTagFiresEvent() {
        $contributorId = 4;
        $sentenceId = 1;
        $expectedTagName = '@needs_native_check';

        $dispatched = false;
        $model = $this->Tag;
        $model->getEventManager()->attach(
            function (Event $event) use ($model, &$dispatched, $expectedTagName) {
                $this->assertSame($model, $event->subject());
                extract($event->data); // $tagName
                $this->assertEquals($expectedTagName, $tagName);
                $dispatched = true;
            },
            'Model.Tag.tagAdded'
        );

        $this->Tag->addTag('@needs_native_check', $contributorId, $sentenceId);

        $this->assertTrue($dispatched);
    }

    function testSentenceOwnerCannotTagOwnSentenceAsOK() {
        $sentenceId = 1;
        $ownerId = 7;
        $before = $this->Tag->TagsSentences->find('count');

        $this->Tag->addTag('OK', $ownerId, $sentenceId);

        $after = $this->Tag->TagsSentences->find('count');
        $added = $after - $before;
        $this->assertEqual(0, $added);
    }
}
