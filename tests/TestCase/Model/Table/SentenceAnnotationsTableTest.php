<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SentenceAnnotationsTable;
use Cake\I18n\I18n;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

class SentenceAnnotationsTableTest extends TestCase {
    public $fixtures = array(
        'app.SentenceAnnotations',
        'app.Users',
        'app.Sentences',
    );

    function setUp() {
        parent::setUp();
        $this->SentenceAnnotation = TableRegistry::getTableLocator()->get('SentenceAnnotations');
        Time::setTestNow(new Time('2020-06-01 01:01:01'));
    }

    function tearDown() {
        unset($this->SentenceAnnotation);
        Time::setTestNow();
        parent::tearDown();
    }

    function testGetLatestAnnotations() {
        $result = $this->SentenceAnnotation->getLatestAnnotations(2);
        $this->assertEquals(2, count($result));
    }

    function testGetAnnotationsForSentenceId() {
        $result = $this->SentenceAnnotation->getAnnotationsForSentenceId(10);
        $this->assertEquals('ちょっと待って。', $result->text);
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
            'user_id' => $userId,
            'modified' => Time::now(),
        );
        $result = array_intersect_key(
            $sentenceAnnotation->toArray(), $expected
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
            'user_id' => $userId,
            'modified' => Time::now(),
        );
        $result = array_intersect_key(
            $sentenceAnnotation->toArray(), $expected
        );

        $this->assertEquals($expected, $result);
    }

    function testSeach() {
        $result = $this->SentenceAnnotation->search('問題');
        $resultIds = Hash::extract($result, '{n}.id');
        $this->assertEquals([1], $resultIds);
    }
}
