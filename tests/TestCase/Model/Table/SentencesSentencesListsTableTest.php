<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SentencesSentencesListsTable;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;

class SentencesSentencesListsTableTest extends TestCase {
    public $fixtures = array(
        'app.SentencesSentencesLists',
        'app.SentencesLists',
        'app.Sentences'
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
        $entity = $this->SentencesSentencesLists->get(2);
        $this->SentencesSentencesLists->sphinxAttributesChanged($attrs, $values, $isMVA, $entity);
        $this->assertTrue($isMVA);
        $this->assertEquals(array('lists_id'), $attrs);
        $this->assertEquals($expectedValues, $values);
    }
}
