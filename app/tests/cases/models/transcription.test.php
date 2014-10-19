<?php
App::import('Model', 'Transcription');

class TranscriptionTestCase extends CakeTestCase {
    var $fixtures = array(
        'app.transcription',
        'app.sentence',
        'app.language',
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
}
