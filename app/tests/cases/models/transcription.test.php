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
        unset($data['id']);
        $data = array_merge($data, $changedFields);
        return (bool)$this->Transcription->save($data);
    }

    function _saveRecordWithout($record, $missingFields) {
        $data = $this->_getRecord($record);
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

    function testSentenceCanBeNullIfParentSet() {
        $this->_assertValidRecordWith(1, array('sentence_id' => null));
    }
    function testParentCanBeNullIfSentenceSet() {
        $this->_assertValidRecordWith(0, array('parent_id' => null));
    }
    function testSentenceOrParentRequired() {
        $this->_assertInvalidRecordWithout(0, array('parent_id', 'sentence_id'));
    }
    function testSentenceXorParentRequired() {
        $this->_assertInvalidRecordWith(0, array('parent_id' => 42));
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
    function testCreatedRequired() {
        $this->_assertInvalidRecordWithout(0, array('created'));
    }

    function testModifiedCantBeEmpty() {
        $this->_assertInvalidRecordWith(0, array('modified' => ''));
    }
    function testModifiedRequired() {
        $this->_assertInvalidRecordWithout(0, array('modified'));
    }
}
