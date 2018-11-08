<?php
namespace App\Test\TestCase\Model;

use App\Audio\Model;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

class AudioTest extends TestCase {
    public $fixtures = array(
        'app.audios',
        'app.contributions',
        'app.favorites_users',
        'app.groups',
        'app.languages',
        'app.links',
        'app.reindex_flags',
        'app.sentences',
        'app.sentence_annotations',
        'app.sentence_comments',
        'app.sentences_lists',
        'app.sentences_sentences_lists',
        'app.tags',
        'app.tags_sentences',
        'app.transcriptions',
        'app.users',
        'app.walls',
        'app.wall_threads'
    );

    function setUp() {
        parent::setUp();
        Configure::write('Search.enabled', true);
        $this->Audio = ClassRegistry::init('Audio');
        $this->AudioFixture = ClassRegistry::init('AudioFixture');
    }

    function tearDown() {
        parent::tearDown();
        unset($this->Audio);
        unset($this->AudioFixture);
        ClassRegistry::flush();
    }

    function _getRecord($record) {
        return $this->AudioFixture->records[$record];
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

        $this->assertEqual($expectedAttributes, $attributes);
        $this->assertEqual($expectedValues, $values);
    }

}
