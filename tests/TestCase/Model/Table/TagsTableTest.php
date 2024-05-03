<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TagsTable;
use App\Test\TestCase\Model\Table\TatoebaTableTestTrait;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Event\EventList;
use Cake\I18n\I18n;

class TagsTableTest extends TestCase {
    use TatoebaTableTestTrait;

    public $fixtures = array(
        'app.Sentences',
        'app.Users',
        'app.Tags',
        'app.TagsSentences',
        'app.UsersLanguages',
    );

    public function setUp() {
        parent::setUp();
        $this->Tag = TableRegistry::getTableLocator()->get('Tags');
        $this->Tag->getEventManager()->setEventList(new EventList());
    }

    public function tearDown() {
        unset($this->Tag);
        parent::tearDown();
    }

    public function testAddTagAddsTag() {
        $contributorId = 4;
        $sentenceId = 1;
        $before = $this->Tag->TagsSentences->find('all')->count();

        $tag = $this->Tag->addTag('@needs_native_check', $contributorId, $sentenceId);

        $after = $this->Tag->TagsSentences->find('all')->count();
        $added = $after - $before;
        $this->assertEquals(1, $added);
        $this->assertEquals(4, $tag->id);
    }

    public function eventTestProvider() {
        return [
            'existing tag' => ['OK', 4, 1],
            'new tag' => ['new tag', 4, 1],
        ];
    }

    /**
     * @dataProvider eventTestProvider
     */
    public function testAddTagFiresEvent($tagName, $userId, $sentenceId) {
        $this->Tag->addTag($tagName, $userId, $sentenceId);

        $this->assertEventFiredWith(
            'Model.Tag.tagAdded',
            'tagName',
            $tagName,
            $this->Tag->getEventManager()
        );
    }



    public function testAddTag_tagAlreadyAdded() {
        $result = $this->Tag->addTag('OK', 1, 2);
        $this->assertTrue($result->link->alreadyExists);
    }

    public function testSentenceOwnerCannotTagOwnSentenceAsOK() {
        $sentenceId = 1;
        $ownerId = 7;
        $before = $this->Tag->TagsSentences->find('all')->count();

        $this->Tag->addTag('OK', $ownerId, $sentenceId);

        $after = $this->Tag->TagsSentences->find('all')->count();
        $added = $after - $before;
        $this->assertEquals(0, $added);
    }

    public function testGetIdFromInternalName_succeeds() {
        $result = $this->Tag->getIdFromInternalName('OK');
        $this->assertEquals(2, $result);
    }

    public function testGetIdFromInternalName_fails() {
        $result = $this->Tag->getIdFromInternalName('OOK');
        $this->assertNull($result);
    }

    public function testGetNameFromId_succeeds() {
        $result = $this->Tag->getNameFromId(2);
        $this->assertEquals('OK', $result);
    }

    public function testGetNameFromId_fails() {
        $result = $this->Tag->getNameFromId(4);
        $this->assertNull($result);
    }

    public function testGetIdFromName_succeeds() {
        $result = $this->Tag->getIdFromName('OK');
        $this->assertEquals(2, $result);
    }

    public function testGetIdFromName_fails() {
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

    public function testAddTag_correctDateUsingArabicLocale() {
        $prevLocale = I18n::getLocale();
        I18n::setLocale('ar');

        $added = $this->Tag->addTag('arabic', 4);
        $returned = $this->Tag->get($added->id);
        $this->assertEquals($added->created->format('Y-m-d H:i:s'), $returned->created->format('Y-m-d H:i:s'));

        I18n::setLocale($prevLocale);
    }

    public function testAddTagWithoutSentenceId_NoDuplicateAdded() {
        $added = $this->Tag->addTag('regional', 4);
        $this->assertNotEquals($added->user_id, 4);
    }

    public function testAddTag_addEmptyTag() {
        $added = $this->Tag->addTag('', 1);
        $this->assertFalse($added);
    }

    public function testAddTag_noTrailingSpaceAfterCuttingTo50Bytes() {
        $tagName = '1234567890123456789012345678901234567890123456789 content after 9 gets cut';
        $expectedName = '1234567890123456789012345678901234567890123456789';
        $added = $this->Tag->addTag($tagName, 4);
        $storedName = $this->Tag->getNameFromId($added->id);
        $this->assertEquals($expectedName, $storedName);
    }
}
