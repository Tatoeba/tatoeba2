<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TagsTable;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Event\Event;

class TagsTableTest extends TestCase {
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

    function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
        $this->Tag = TableRegistry::getTableLocator()->get('Tags');
    }

    function tearDown() {
        unset($this->Tag);
        parent::tearDown();
    }

    function testAddTagAddsTag() {
        $contributorId = 4;
        $sentenceId = 1;
        $before = $this->Tag->TagsSentences->find('all')->count();

        $this->Tag->addTag('@needs_native_check', $contributorId, $sentenceId);

        $after = $this->Tag->TagsSentences->find('all')->count();
        $added = $after - $before;
        $this->assertEquals(1, $added);
    }

    function testAddTagFiresEvent() {
        $contributorId = 4;
        $sentenceId = 1;
        $expectedTagName = '@needs_native_check';

        $dispatched = false;
        $model = $this->Tag;
        $model->getEventManager()->on(
            'Model.Tag.tagAdded',
            function (Event $event) use ($model, &$dispatched, $expectedTagName) {
                $this->assertSame($model, $event->getSubject());
                extract($event->getData()); // $tagName
                $this->assertEquals($expectedTagName, $tagName);
                $dispatched = true;
            }
        );

        $this->Tag->addTag('@needs_native_check', $contributorId, $sentenceId);

        $this->assertTrue($dispatched);
    }

    function testSentenceOwnerCannotTagOwnSentenceAsOK() {
        $sentenceId = 1;
        $ownerId = 7;
        $before = $this->Tag->TagsSentences->find('all')->count();

        $this->Tag->addTag('OK', $ownerId, $sentenceId);

        $after = $this->Tag->TagsSentences->find('all')->count();
        $added = $after - $before;
        $this->assertEquals(0, $added);
    }
}
