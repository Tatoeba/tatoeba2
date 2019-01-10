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
        'app.tags',
        'app.tags_sentences',
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

        $tagId = $this->Tag->addTag('@needs_native_check', $contributorId, $sentenceId);

        $after = $this->Tag->TagsSentences->find('all')->count();
        $added = $after - $before;
        $this->assertEquals(1, $added);
        $this->assertEquals(4, $tagId);
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

    function testAddTag_tagAlreadyAdded() {
        $result = $this->Tag->addTag('OK', 1, 2);
        $this->assertEmpty($result);
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

    function testGetIdFromInternalName_succeeds() {
        $result = $this->Tag->getIdFromInternalName('OK');
        $this->assertEquals(2, $result);
    }

    function testGetIdFromInternalName_fails() {
        $result = $this->Tag->getIdFromInternalName('OOK');
        $this->assertNull($result);
    }

    function testGetNameFromId_succeeds() {
        $result = $this->Tag->getNameFromId(2);
        $this->assertEquals('OK', $result);
    }

    function testGetNameFromId_fails() {
        $result = $this->Tag->getNameFromId(4);
        $this->assertNull($result);
    }

    function testGetIdFromName_succeeds() {
        $result = $this->Tag->getIdFromName('OK');
        $this->assertEquals(2, $result);
    }

    function testGetIdFromName_fails() {
        $result = $this->Tag->getIdFromName('OOK');
        $this->assertEquals(null, $result);
    }
}
