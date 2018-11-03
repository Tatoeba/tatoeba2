<?php
App::uses('SentenceAnnotation', 'Model');

class SentenceAnnotationTest extends CakeTestCase {
    public $fixtures = array(
        'app.sentence_annotation',
    );

    function setUp() {
        parent::setUp();
        $this->SentenceAnnotation = ClassRegistry::init('SentenceAnnotation');
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
        $this->SentenceAnnotation->saveAnnotation($data, $userId);

        $sentenceAnnotation = $this->SentenceAnnotation->findById(1);
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
