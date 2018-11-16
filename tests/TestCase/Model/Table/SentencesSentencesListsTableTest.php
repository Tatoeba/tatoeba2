<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SentencesSentencesListsTable;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;

class SentencesSentencesListsTableTest extends TestCase {
    public $fixtures = array(
        'app.sentences_sentences_lists',
        'app.sentences_lists',
        'app.sentences'
    );

    function setUp() {
        parent::setUp();
        $this->SentencesSentencesLists = TableRegistry::getTableLocator()->get('SentencesSentencesLists');
    }

    function tearDown() {
        unset($this->SentencesSentencesLists);
        parent::tearDown();
    }

    function testSphinxAttributesChanged() {
        $expectedValues = array(8 => array(array(1)));
        $sentenceId = 8;
        $this->SentencesSentencesLists->sphinxAttributesChanged($attrs, $values, $isMVA, $sentenceId);
        $this->assertTrue($isMVA);
        $this->assertEquals(array('lists_id'), $attrs);
        $this->assertEquals($expectedValues, $values);
    }
}
