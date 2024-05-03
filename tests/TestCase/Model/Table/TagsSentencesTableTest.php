<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TagsSentencesTable;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\I18n\I18n;

class TagsSentencesTableTest extends TestCase {
    public $fixtures = array(
        'app.Sentences',
        'app.TagsSentences'
    );

    function setUp() {
        parent::setUp();
        $this->TagsSentences = TableRegistry::getTableLocator()->get('TagsSentences');
    }

    function tearDown() {
        unset($this->TagsSentences);
        parent::tearDown();
    }

    function testSphinxAttributesChanged() {
        $expectedValues = array(8 => array(array(1, 3)));
        $entity = $this->TagsSentences->get(1);
        $this->TagsSentences->sphinxAttributesChanged($attrs, $values, $isMVA, $entity);
        $this->assertTrue($isMVA);
        $this->assertEquals(array('tags_id'), $attrs);
        $this->assertEquals($expectedValues, $values);
    }

    function testTagSentence_succeeds() {
        $result = $this->TagsSentences->tagSentence(1, 1, 1);
        $this->assertEquals(4, $result->id);
        $this->assertFalse($result->alreadyExists);
    }

    function testTagSentence_failsBecauseAlreadyAdded() {
        $result = $this->TagsSentences->tagSentence(2, 2, 1);
        $this->assertTrue($result->alreadyExists);
    }

    function testTagSentence_correctDateUsingArabicLocale() {
        $prevLocale = I18n::getLocale();
        I18n::setLocale('ar');

        $added = $this->TagsSentences->tagSentence(1, 2, 3);
        $returned = $this->TagsSentences->get($added->id);
        $this->assertEquals($added->added_time->format('Y-m-d H:i:s'), $returned->added_time->format('Y-m-d H:i:s'));

        I18n::setLocale($prevLocale);
    }

    function testRemoveTagFromSentence_succeeds() {
        $entity = $this->TagsSentences->get(1);
        $rv = $this->TagsSentences->removeTagFromSentence(
            $entity->tag_id,
            $entity->sentence_id
        );
        $this->assertTrue($rv);
        $this->assertNull($this->TagsSentences->findById(1)->first());
    }

    function testRemoveTagFromSentence_worksWithDuplicates() {
        $this->TagsSentences->getConnection()->insert('tags_sentences',
            [
                'tag_id' => 1,
                'user_id' => 4,
                'sentence_id' => 8,
                'added_time' => '2018-04-05 22:20:12',
            ]);
        $rv = $this->TagsSentences->removeTagFromSentence(1, 8);
        $this->assertTrue($rv);
        $this->assertFalse($this->TagsSentences->exists(['tag_id' => 1, 'sentence_id' => 8]));
    }
}
