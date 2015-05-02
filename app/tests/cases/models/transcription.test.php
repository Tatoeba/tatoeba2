<?php
App::import('Model', 'Transcription');

class TranscriptionTestCase extends CakeTestCase {
    var $fixtures = array(
        'app.contribution',
        'app.country',
        'app.favorites_user',
        'app.group',
        'app.language',
        'app.link',
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
    function testScriptCantBeUpdated() {
        $this->Transcription->delete(1); // to avoid uniqness error
        $data = array('id' => 2, 'script' => 'Hrkt');

        $result = (bool)$this->Transcription->save($data);

        $this->assertFalse($result);
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
    function testSentenceIdCantBeUpdated() {
        $this->Transcription->delete(3); // to avoid uniqness error
        $data = array('id' => 1, 'sentence_id' => 10);

        $result = (bool)$this->Transcription->save($data);

        $this->assertFalse($result);
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

    function testTranscriptionMustBeUniqueForASentenceAndAScriptOnCreate() {
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
        $this->assertTrue(in_array('Hrkt', $result));
    }
    function testJapaneseCanBeTranscriptedToRomaji() {
        $jpnSentence = $this->Transcription->Sentence->find('first', array(
            'conditions' => array('Sentence.lang' => 'jpn')
        ));
        $result = $this->Transcription->transcriptableToWhat($jpnSentence);
        $this->assertTrue(in_array('Latn', $result));
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

    function _installAutotranscriptionMock() {
        Mock::generate('Autotranscription');
        $autotranscription =& new MockAutotranscription;
        $this->Transcription->setAutotranscription($autotranscription);
        return $autotranscription;
    }

    function testGenerateTranscriptionCallsGenerator() {
        $jpnSentence = $this->Transcription->Sentence->find('first', array(
            'conditions' => array('Sentence.lang' => 'jpn')
        ));
        $this->_installAutotranscriptionMock()->expectOnce(
            '_getFurigana',
            array($jpnSentence['Sentence']['text'])
        );

        $this->Transcription->generateTranscription($jpnSentence, 'Hrkt');
    }

    function testGenerateTranscriptionChains() {
        $transcript = $this->Transcription->find('first', array(
            'conditions' => array('Transcription.id' => 1),
            'contain' => array('Sentence')
        ));
        $this->_installAutotranscriptionMock()->expectOnce(
            'tokenizedJapaneseWithReadingsToRomaji',
            array($transcript['Transcription']['text'])
        );

        $this->Transcription->generateTranscription($transcript['Sentence'], 'Latn');
    }

    function testGenerateTranscriptionReturnsTranscription() {
        $jpnSentence = $this->Transcription->Sentence->findById(6);
        $this->_installAutotranscriptionMock()->setReturnValue('_getFurigana', 'stuff');

        $result = $this->Transcription->generateTranscription($jpnSentence, 'Hrkt');

        $expected = array(
            'sentence_id' => 6,
            'parent_id' => null,
            'script' => 'Hrkt',
            'text' => 'stuff',
            'dirty' => false,
        );
        $this->assertEqual($expected, $result);
    }

    function testGenerateTranscriptionReturnsTranscriptionWithParent() {
        $jpnSentence = $this->Transcription->Sentence->findById(6);
        $this->_installAutotranscriptionMock()
             ->setReturnValue('tokenizedJapaneseWithReadingsToRomaji',
                              'stuff in Latin');

        $result = $this->Transcription->generateTranscription($jpnSentence, 'Latn');

        $expected = array(
            'sentence_id' => 6,
            'parent_id' => 1,
            'script' => 'Latn',
            'text' => 'stuff in Latin',
            'dirty' => false,
        );
        $this->assertEqual($expected, $result);
    }

    function testGenerateAndSaveAllTranscriptionsFor() {
        $this->Transcription->deleteAll('1=1');
        $jpnSentence = $this->Transcription->Sentence->findById(6);
        $autotranscription = $this->_installAutotranscriptionMock();
        $autotranscription->setReturnValue('tokenizedJapaneseWithReadingsToRomaji', 'stuff in Latin');
        $autotranscription->setReturnValue('_getFurigana', 'stuff in kana');

        $this->Transcription->generateAndSaveAllTranscriptionsFor($jpnSentence);

        $created = $this->Transcription->find('count', array(
            'conditions' => array('sentence_id' => 6)
        ));
        $this->assertEqual(2, $created);
    }
}
