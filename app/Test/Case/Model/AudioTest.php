<?php
App::import('Model', 'Audio');

class AudioTest extends CakeTestCase {
    var $fixtures = array(
        'app.audio',
        'app.contribution',
        'app.favorites_user',
        'app.group',
        'app.language',
        'app.link',
        'app.reindex_flag',
        'app.sentence',
        'app.sentence_annotation',
        'app.sentence_comment',
        'app.sentences_list',
        'app.sentences_sentences_list',
        'app.tag',
        'app.tags_sentence',
        'app.transcription',
        'app.user',
        'app.wall',
        'app.wall_thread',
    );

    function startTest($method) {
        $this->Audio =& ClassRegistry::init('Audio');
    }

    function endTest($method) {
        unset($this->Audio);
        ClassRegistry::flush();
    }

    function _getRecord($record) {
        return $this->_fixtures['app.audio']->records[$record];
    }

    function _saveRecordWith($record, $changedFields) {
        $data = $this->_getRecord($record);
        $this->Audio->deleteAll(array('1=1'));
        unset($data['id']);
        $data = array_merge($data, $changedFields);
        return (bool)$this->Audio->save($data);
    }

    function _saveRecordWithout($record, $missingFields) {
        $data = $this->_getRecord($record);
        $this->Audio->deleteAll(array('1=1'));
        unset($data['id']);
        foreach ($missingFields as $field) {
            unset($data[$field]);
        }
        return (bool)$this->Audio->save($data);
    }

    function _assertValidRecordWith($record, $changedFields) {
        $this->assertTrue($this->_saveRecordWith($record, $changedFields));
    }
    function _assertValidRecordWithout($record, $changedFields) {
        $this->assertTrue($this->_saveRecordWithout($record, $changedFields));
    }
    function _assertInvalidRecordWith($record, $changedFields) {
        $this->assertFalse($this->_saveRecordWith($record, $changedFields));
    }
    function _assertInvalidRecordWithout($record, $missingFields) {
        $this->assertFalse($this->_saveRecordWithout($record, $missingFields));
    }

    function testValidateFirstRecord() {
        $this->_assertValidRecordWith(0, array());
    }

    function testSentenceIdCantBeEmpty() {
        $this->_assertInvalidRecordWith(0, array('sentence_id' => null));
    }
    function testSentenceIdRequired() {
        $this->_assertInvalidRecordWithout(0, array('sentence_id'));
    }
    function testSentenceIdCanBeUpdated() {
        $data = array('id' => 1, 'sentence_id' => 10);

        $result = (bool)$this->Audio->save($data);

        $this->assertTrue($result);
    }

    function testCreatedCantBeEmpty() {
        $this->_assertInvalidRecordWith(0, array('created' => ''));
    }
    function testCreatedIsAutomaticallySet() {
        $this->_assertValidRecordWithout(0, array('created'));
    }

    function testModifiedCantBeEmpty() {
        $this->_assertInvalidRecordWith(0, array('modified' => ''));
    }
    function testModifiedIsAutomaticallySet() {
        $this->_assertValidRecordWithout(0, array('modified'));
    }

    function testUserIdMustBeNumeric() {
        $this->_assertInvalidRecordWith(0, array('user_id' => 'melon'));
    }

    function testMustHaveEitherExternalOrUserIdSet() {
        $this->_assertInvalidRecordWithout(0, array('user_id'));
        $this->_assertInvalidRecordWithout(1, array('external'));
    }
    function testExternalCantBeEmptyIfUserIdNotSet() {
        $this->_assertInvalidRecordWith(1, array('external' => ''));
    }

    function testSentencesReindexedOnSentenceIdUpdate() {
        $audioId = 1;
        $prevAudio = $this->Audio->findById($audioId, 'sentence_id');
        $data = array('id' => $audioId, 'sentence_id' => 7);
        $expected = array(1, 2, 3, 4, 7);

        $this->Audio->save($data);

        $result = $this->Audio->Sentence->ReindexFlag->find('all', array(
            'order' => 'sentence_id'
        ));
        $result = Set::classicExtract($result, '{n}.ReindexFlag.sentence_id');
        $this->assertEqual($expected, $result);
    }

    function testSentencesReindexedOnCreate() {
        $expected = array(4, 6, 10);

        $this->_saveRecordWith(0, array('sentence_id' => 10));

        $result = $this->Audio->Sentence->ReindexFlag->find('all', array(
            'order' => 'sentence_id'
        ));
        $result = Set::classicExtract($result, '{n}.ReindexFlag.sentence_id');
        $this->assertEqual($expected, $result);
    }

    function testSentencesReindexedOnDelete() {
        $expected = array(1, 2, 3, 4);

        $this->Audio->delete(1, false);

        $result = $this->Audio->Sentence->ReindexFlag->find('all', array(
            'order' => 'sentence_id'
        ));
        $result = Set::classicExtract($result, '{n}.ReindexFlag.sentence_id');
        $this->assertEqual($expected, $result);
    }

    function testSphinxAttributesChanged_onUpdate() {
        $audioId = 1;
        $sentenceId = 3;
        $expectedAttributes = array('has_audio');
        $expectedValues = array(
            $sentenceId => array(1),
        );

        $this->Audio->id = $sentenceId;
        $this->Audio->data['Audio'] = array(
            'id' => $audioId,
            'sentence_id' => $sentenceId,
        );
        $this->Audio->sphinxAttributesChanged($attributes, $values, $isMVA);

        $this->assertFalse($isMVA);
        $this->assertEqual($expectedAttributes, $attributes);
        $this->assertEqual($expectedValues, $values);
    }

    function testSphinxAttributesChanged_onDelete() {
        $audioId = 1;
        $sentenceId = 1;
        $expectedAttributes = array('has_audio');
        $expectedValues = array(
            $sentenceId => array(0),
        );

        $this->Audio->id = $sentenceId;
        $this->Audio->data['Audio'] = array(
            'id' => $audioId,
            'sentence_id' => $sentenceId,
        );
        $this->Audio->sphinxAttributesChanged($attributes, $values, $isMVA);

        $this->assertFalse($isMVA);
        $this->assertEqual($expectedAttributes, $attributes);
        $this->assertEqual($expectedValues, $values);
    }

    function testGetAudioStats() {
        $expected = array(
            array('lang' => 'fra', 'total' => 2),
            array('lang' => 'spa', 'total' => 1),
        );

        $result = $this->Audio->getAudioStats();

        $this->assertEqual($expected, $result);
    }

}
