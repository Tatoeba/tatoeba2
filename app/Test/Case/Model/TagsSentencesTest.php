<?php
/* TagsSentences Test cases generated on: 2015-06-25 11:02:51 : 1435230171*/
App::import('Model', 'TagsSentences');

class TagsSentencesTest extends CakeTestCase {
    public $fixtures = array(
        'app.tags_sentence',
    );

    function startTest($method) {
        $this->TagsSentences = ClassRegistry::init('TagsSentences');
    }

    function endTest($method) {
        unset($this->TagsSentences);
        ClassRegistry::flush();
    }

    function testSphinxAttributesChanged() {
        $expectedValues = array(8 => array(array(1, 3)));
        $this->TagsSentences->data['TagsSentences'] = array(
            'sentence_id' => '8',
        );
        $this->TagsSentences->sphinxAttributesChanged($attrs, $values, $isMVA);
        $this->assertTrue($isMVA);
        $this->assertEqual(array('tags_id'), $attrs);
        $this->assertEqual($expectedValues, $values);
    }
}
