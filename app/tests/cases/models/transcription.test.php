<?php
App::import('Model', 'Transcription');

class TranscriptionTestCase extends CakeTestCase {
    var $fixtures = array(
        'app.favorites_user',
        'app.language',
        'app.link',
        'app.sentence',
        'app.sentence_annotation',
        'app.sentences_sentences_list',
        'app.tag',
        'app.tags_sentence',
        'app.transcription',
        'app.wall',
        'app.wall_thread',
    );

    function startTest() {
        $this->Transcription =& ClassRegistry::init('Transcription');
    }

    function endTest() {
        unset($this->Transcription);
        ClassRegistry::flush();
    }

    function _getRecord($record) {
        return $this->_fixtures['app.transcription']->records[$record];
    }

    function _saveRecordWith($record, $changedFields) {
        $data = $this->_getRecord($record);
        $this->Transcription->deleteAll(array('1=1'));
        unset($data['id']);
        $data = array_merge($data, $changedFields);
        return (bool)$this->Transcription->save($data);
    }

    function _saveRecordWithout($record, $missingFields) {
        $data = $this->_getRecord($record);
        $this->Transcription->deleteAll(array('1=1'));
        unset($data['id']);
        foreach ($missingFields as $field) {
            unset($data[$field]);
        }
        return (bool)$this->Transcription->save($data);
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
    function testValidateSecondRecord() {
        $this->_assertValidRecordWith(1, array());
    }

    function testScriptMustBeValid() {
        $this->_assertInvalidRecordWith(0, array('script' => 'ABCD'));
    }
    function testScriptRequired() {
        $this->_assertInvalidRecordWithout(0, array('script'));
    }

    function testTextCantBeEmpty() {
        $this->_assertInvalidRecordWith(0, array('text' => ''));
    }
    function testTextRequired() {
        $this->_assertInvalidRecordWithout(0, array('text'));
    }

    function testSentenceIdCantBeEmpty() {
        $this->_assertInvalidRecordWith(0, array('sentence_id' => null));
    }
    function testSentenceIdRequired() {
        $this->_assertInvalidRecordWithout(0, array('sentence_id'));
    }

    function testParentIdMustBeNumeric() {
        $this->_assertInvalidRecordWith(0, array('parent_id' => 'melon'));
    }
    function testParentIdNotRequired() {
        $this->_assertValidRecordWithout(0, array('parent_id'));
    }

    function testDirtyMustBeBoolean() {
        $this->_assertInvalidRecordWith(0, array('dirty' => 'liberty'));
    }
    function testDirtyRequired() {
        $this->_assertInvalidRecordWithout(0, array('dirty'));
    }

    function testCreatedCantBeEmpty() {
        $this->_assertInvalidRecordWith(0, array('created' => ''));
    }
    function testCreatedIsAutomaticallySet() {
        $this->_assertValidRecordWithout(0, array('created'));
    }

    function testUserModifiedMustBeBoolean() {
        $this->_assertInvalidRecordWith(0, array('user_modified' => 'lemon'));
    }
    function testUserModifiedIsAutomaticallySet() {
        $this->_assertValidRecordWithout(0, array('user_modified'));
    }

    function testModifiedCantBeEmpty() {
        $this->_assertInvalidRecordWith(0, array('modified' => ''));
    }
    function testModifiedIsAutomaticallySet() {
        $this->_assertValidRecordWithout(0, array('created'));
    }

    function testTranscriptionMustBeUniqueForASentenceAndAScript() {
        $data = $this->_getRecord(0);
        unset($data['id']);

        $result = (bool)$this->Transcription->save($data);

        $this->assertFalse($result);
    }

    function testJapaneseCanBeTranscriptedToKanas() {
        $jpnSentence = $this->Transcription->Sentence->find('first', array(
            'conditions' => array('Sentence.lang' => 'jpn')
        ));
        $result = $this->Transcription->transcriptableToWhat($jpnSentence);
        $this->assertTrue(array_key_exists('Hrkt', $result));
    }
    function testJapaneseCanBeTranscriptedToRomaji() {
        $jpnSentence = $this->Transcription->Sentence->find('first', array(
            'conditions' => array('Sentence.lang' => 'jpn')
        ));
        $result = $this->Transcription->transcriptableToWhat($jpnSentence);
        $this->assertTrue(array_key_exists('Latn', $result));
    }

    function testEditTrancriptionText() {
        $result = $this->Transcription->save(array(
            'id' => 2, 'text' => 'we change this'
        ));
        $this->assertTrue($result);
    }
    function testEditTrancriptionTextCantBeEmpty() {
        $result = $this->Transcription->save(array(
            'id' => 2, 'text' => ''
        ));
        $this->assertFalse($result);
    }

    function testEditScript() {
        $this->Transcription->delete(2); // to avoid uniqness error
        $result = $this->Transcription->save(array(
            'id' => 1, 'script' => 'Latn'
        ));
        $this->assertTrue($result);
    }
    function testEditScriptMustStillBeAScript() {
        $result = $this->Transcription->save(array(
            'id' => 2, 'script' => 'that’s not an script'
        ));
        $this->assertFalse($result);
    }

    function testCantSaveTranscriptionWithoutInvalidParent() {
        $nonexistantSentenceId = 52715278;
        $result = $this->Transcription->save(array(
            'sentence_id' => $nonexistantSentenceId,
            'script' => 'Latn',
            'text' => 'Transcription with invalid parent.',
            'dirty' => 0,
        ));
        $this->assertFalse($result);
    }

    function testCantSaveNotAllowedTranscriptionOnInsert() {
        $englishSentenceId = 1;
        $result = $this->Transcription->save(array(
            'sentence_id' => $englishSentenceId,
            'script' => 'Latn',
            'text' => 'Transcript of English into Latin script??',
            'dirty' => 0,
        ));
        $this->assertFalse($result);
    }

    function testCantSaveNotAllowedTranscriptionOnUpdate() {
        $result = $this->Transcription->save(array(
            'id' => 1,
            'script' => 'Jpan',
            'text' => 'Transcript of Japanese into Japanese??',
        ));
        $this->assertFalse($result);
    }
}
