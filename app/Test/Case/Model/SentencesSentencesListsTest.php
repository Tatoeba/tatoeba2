<?php
/* SentencesSentencesLists Test cases generated on: 2016-12-03 08:56:44 : 1480755404*/
App::import('Model', 'SentencesSentencesLists');

class SentencesSentencesListsTest extends CakeTestCase {
    public $fixtures = array(
        'app.sentences_sentences_lists',
        'app.sentence',
        'app.language',
        'app.user',
        'app.group',
        'app.wall',
        'app.wall_thread',
        'app.sentence_comments',
        'app.contributions',
        'app.sentences_lists',
        'app.favorites_user',
        'app.translation',
        'app.transcription',
        'app.contribution',
        'app.sentence_comment',
        'app.sentence_annotation',
        'app.reindex_flag',
        'app.link',
        'app.sentences_translation',
        'app.tag',
        'app.tag_sentences',
        'app.tags_sentence',
        'app.sentences_list',
        'app.sentences_sentences_list'
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
