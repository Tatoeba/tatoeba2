<?php
/* TagsSentences Test cases generated on: 2015-06-25 11:02:51 : 1435230171*/
App::import('Model', 'TagsSentences');

class TagsSentencesTest extends CakeTestCase {
    public $fixtures = array(
        'app.tags_sentence',
        'app.user',
        'app.group',
        'app.wall',
        'app.wall_thread',
        'app.sentence_comment',
        'app.contribution',
        'app.sentence',
        'app.sentences_list',
        'app.favorite',
        'app.sentence',
        'app.language',
        'app.reindex_flag',
        'app.favorites_user',
        'app.link',
        'app.contribution',
        'app.sentence_comment',
        'app.sentence_annotation',
        'app.translation',
        'app.sentences_translation',
        'app.tag',
        'app.sentences_list',
        'app.sentences_sentences_list',
        'app.favorites_user',
        'app.transcription',
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
