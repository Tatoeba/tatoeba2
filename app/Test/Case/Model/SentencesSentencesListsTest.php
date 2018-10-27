<?php
/* SentencesSentencesLists Test cases generated on: 2016-12-03 08:56:44 : 1480755404*/
App::import('Model', 'SentencesSentencesLists');

class SentencesSentencesListsTest extends CakeTestCase {
    public $fixtures = array(
        'app.sentences_sentences_list',
        'app.sentences_list',
        'app.sentence',
    );

    function startTest($method) {
        $this->SentencesSentencesLists = ClassRegistry::init('SentencesSentencesLists');
    }

    function endTest($method) {
        unset($this->SentencesSentencesLists);
        ClassRegistry::flush();
    }

    function testSphinxAttributesChanged() {
        $expectedValues = array(8 => array(array(1)));
        $this->SentencesSentencesLists->data['SentencesSentencesLists'] = array(
            'sentence_id' => '8',
            'sentences_list_id' => 2,
        );
        $this->SentencesSentencesLists->sphinxAttributesChanged($attrs, $values, $isMVA);
        $this->assertTrue($isMVA);
        $this->assertEqual(array('lists_id'), $attrs);
        $this->assertEqual($expectedValues, $values);
    }
}
