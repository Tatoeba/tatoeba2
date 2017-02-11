<?php
App::import('Model', 'Tag');

class TagTest extends CakeTestCase {
    public $fixtures = array(
        'app.sentence',
        'app.user',
        'app.group',
        'app.sentence_comment',
        'app.contribution',
        'app.sentences_list',
        'app.sentences_sentences_list',
        'app.wall',
        'app.wall_thread',
        'app.favorites_user',
        'app.tag',
        'app.tags_sentence',
        'app.language',
        'app.link',
        'app.sentence_annotation',
        'app.transcription',
        'app.reindex_flag',
    );

    function startTest($method) {
        $this->Tag = ClassRegistry::init('Tag');
    }

    function endTest($method) {
        unset($this->Tag);
        ClassRegistry::flush();
    }

    function testAddTagAddsTag() {
        $contributorId = 4;
        $sentenceId = 1;
        $before = $this->Tag->TagsSentences->find('count');

        $this->Tag->addTag('@needs_native_check', $contributorId, $sentenceId);

        $after = $this->Tag->TagsSentence->find('count');
        $added = $after - $before;
        $this->assertEqual(1, $added);
    }

    function testSentenceOwnerCannotTagOwnSentenceAsOK() {
        $sentenceId = 1;
        $ownerId = 7;
        $before = $this->Tag->TagsSentences->find('count');

        $this->Tag->addTag('OK', $ownerId, $sentenceId);

        $after = $this->Tag->TagsSentence->find('count');
        $added = $after - $before;
        $this->assertEqual(0, $added);
    }
}
