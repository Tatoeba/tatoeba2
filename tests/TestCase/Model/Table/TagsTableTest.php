<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TagsTable;
use App\Test\TestCase\Model\Table\TatoebaTableTestTrait;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Event\Event;

class TagsTableTest extends TestCase {
    use TatoebaTableTestTrait;

    public $fixtures = array(
        'app.sentences',
        'app.users',
        'app.tags',
        'app.tags_sentences',
        'app.users_languages',
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

        $tag = $this->Tag->addTag('@needs_native_check', $contributorId, $sentenceId);

        $after = $this->Tag->TagsSentences->find('all')->count();
        $added = $after - $before;
        $this->assertEquals(1, $added);
        $this->assertEquals(4, $tag->id);
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
        $this->assertTrue($result->alreadyExists);
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

    public function removeAsUser($username, $tagId, $sentenceId) {
        $beforeCount = $this->Tag->TagsSentences->find()
            ->where(['tag_id' => $tagId, 'sentence_id' => $sentenceId])
            ->count();
        if ($username) {
            $this->logInAs($username);
        }

        $this->Tag->removeTagFromSentence($tagId, $sentenceId);

        $afterCount = $this->Tag->TagsSentences->find()
            ->where(['tag_id' => $tagId, 'sentence_id' => $sentenceId])
            ->count();
        return $afterCount - $beforeCount;
    }

    public function testGuestDoesntRemoveTag() {
        $delta = $this->removeAsUser(null, 2, 2);
        $this->assertEquals(0, $delta);
    }

    public function testRegularUserDoesNotRemoveTag() {
        $delta = $this->removeAsUser('contributor', 1, 8);
        $this->assertEquals(0, $delta);
    }

    public function testAdvancedUserAuthorDoesRemoveTag() {
        $delta = $this->removeAsUser('advanced_contributor', 2, 2);
        $this->assertEquals(-1, $delta);
    }

    public function testDifferentAdvancedUserDoesNotRemoveTag() {
        $delta = $this->removeAsUser('advanced_contributor', 1, 8);
        $this->assertEquals(0, $delta);
    }

    public function testCorpusMaintainerDoesRemoveTag() {
        $delta = $this->removeAsUser('corpus_maintainer', 2, 2);
        $this->assertEquals(-1, $delta);
    }

    public function testAdminDoesRemoveTag() {
        $delta = $this->removeAsUser('admin', 2, 2);
        $this->assertEquals(-1, $delta);
    }
}
