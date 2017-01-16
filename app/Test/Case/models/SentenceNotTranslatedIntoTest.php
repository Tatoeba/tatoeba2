<?php
/* SentenceNotTranslatedInto Test cases generated on: 2016-02-08 17:31:23 : 1454952683*/
App::import('Model', 'SentenceNotTranslatedInto');

class SentenceNotTranslatedIntoTest extends CakeTestCase {
    var $fixtures = array(
        'app.audio',
        'app.contribution',
        'app.group',
        'app.favorites_user',
        'app.sentence',
        'app.sentence_annotation',
        'app.sentence_comment',
        'app.sentence_not_translated_into',
        'app.sentences_list',
        'app.sentences_sentences_list',
        'app.language',
        'app.link',
        'app.reindex_flag',
        'app.tag',
        'app.tags_sentence',
        'app.transcription',
        'app.user',
        'app.wall',
        'app.wall_thread',
    );

    public function startTest($method) {
        $this->SNTI =& ClassRegistry::init('SentenceNotTranslatedInto');
        $this->Sentence =& ClassRegistry::init('Sentence');
    }

    public function endTest($method) {
        unset($this->SNTI);
        ClassRegistry::flush();
    }

    public function testPaginateAllSentences_LonelyAndWithTranslation() {
        $expectedIds = array(7, 1);
        $returnedIds = $this->_runPaginate('eng', 'none', false);
        $this->assertEqual($expectedIds, $returnedIds);
    }

    public function testPaginateAllSentences_LonelyAndWithTranslation_Paginate() {
        $expectedIds = array(7);
        $returnedIds = $this->_runPaginate('eng', 'none', false, 1, 1);
        $this->assertEqual($expectedIds, $returnedIds);

        $expectedIds = array(1);
        $returnedIds = $this->_runPaginate('eng', 'none', false, 2, 1);
        $this->assertEqual($expectedIds, $returnedIds);
    }

    public function testPaginateAllSentences_LonelyAndWithTranslation_WithAudio() {
        $expectedIds = array(12, 4);
        $returnedIds = $this->_runPaginate('fra', 'none', true);
        $this->assertEqual($expectedIds, $returnedIds);
    }

    public function testPaginateAllSentences_Lonely() {
        $expectedIds = array(12, 8);
        $returnedIds = $this->_runPaginate('fra', 'und', false);
        $this->assertEqual($expectedIds, $returnedIds);
    }

    public function testPaginateAllSentences_Lonely_WithAudio() {
        $expectedIds = array(12);
        $returnedIds = $this->_runPaginate('fra', 'und', true);
        $this->assertEqual($expectedIds, $returnedIds);
    }

    public function testPaginateAllSentencesWithoutTranslation() {
        $expectedIds = array(7);
        $returnedIds = $this->_runPaginate('eng', 'und', false);
        $this->assertEqual($expectedIds, $returnedIds);
    }

    public function testPaginateAllSentencesWithoutTranslation_WithAudio() {
        $expectedIds = array();
        $returnedIds = $this->_runPaginate('eng', 'und', true);
        $this->assertEqual($expectedIds, $returnedIds);
    }

    public function _runPaginate($lang, $notTranslatedInto, $audioOnly, $page = 1, $limit = 10) {
        $results = $this->SNTI->paginate(
            array( /* conditions */
                'source' => $lang,
                'notTranslatedInto' => $notTranslatedInto,
                'audioOnly' => $audioOnly,
            ),
            array('id'), /* fields */
            'Sentence.id DESC', /* order */
            $limit,      /* limit */
            $page,       /* page */
            1,           /* recursive */
            array()      /* extra parameters to find() */
        );
        $ids = Set::extract($results, '{n}.Sentence.id');
        return $ids;
    }
}
