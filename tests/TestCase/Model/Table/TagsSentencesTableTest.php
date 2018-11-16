<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TagsSentencesTable;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;

class TagsSentencesTableTest extends TestCase {
    public $fixtures = array(
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
        $sentenceId = 8;
        $this->TagsSentences->sphinxAttributesChanged($attrs, $values, $isMVA, $sentenceId);
        $this->assertTrue($isMVA);
        $this->assertEquals(array('tags_id'), $attrs);
        $this->assertEquals($expectedValues, $values);
    }
}
