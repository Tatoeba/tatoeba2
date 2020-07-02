<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TagsSentencesTable;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\I18n\I18n;

class TagsSentencesTableTest extends TestCase {
    public $fixtures = array(
        'app.sentences',
        'app.tags_sentences'
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
        $this->assertEquals($added->added_time, $returned->added_time);

        I18n::setLocale($prevLocale);
    }
}
