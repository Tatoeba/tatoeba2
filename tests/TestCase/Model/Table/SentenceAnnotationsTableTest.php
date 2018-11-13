<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SentenceAnnotationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class SentenceAnnotationsTableTest extends TestCase {
    public $fixtures = array(
        'app.sentence_annotations'
    );

    function setUp() {
        parent::setUp();
        $this->SentenceAnnotation = TableRegistry::getTableLocator()->get('SentenceAnnotations');
    }

    function tearDown() {
        unset($this->SentenceAnnotation);
        parent::tearDown();
    }

    function testSaveAnnotation_addsAnnotation() {
        $userId = 1;
        $data = array(
            'sentence_id' => 6,
            'meaning_id' => 1,
            'text' => ' Trim me please '
        );
        $sentenceAnnotation = $this->SentenceAnnotation->saveAnnotation(
            $data, $userId
        );
        
        $expected = array(
            'sentence_id' => 6,
            'meaning_id' => 1,
            'text' => 'Trim me please',
            'user_id' => $userId
        );
        $result = array_intersect_key(
            $sentenceAnnotation['SentenceAnnotation'], $expected
        );

        $this->assertEquals($expected, $result);
    }

    function testSaveAnnotation_editsAnnotation() {
        $userId = 4;
        $data = array(
            'id' => 1,
            'sentence_id' => 6,
            'meaning_id' => 1,
            'text' => 'Some new text'
        );
        $sentenceAnnotation = $this->SentenceAnnotation->saveAnnotation(
            $data, $userId
        );
        
        $expected = array(
            'id' => 1,
            'sentence_id' => 6,
            'meaning_id' => 1,
            'text' => 'Some new text',
            'user_id' => $userId
        );
        $result = array_intersect_key(
            $sentenceAnnotation['SentenceAnnotation'], $expected
        );

        $this->assertEquals($expected, $result);
    }
}
