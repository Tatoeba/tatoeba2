<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AudiosTable;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use App\Test\Fixture\AudiosFixture;
use Cake\Utility\Hash;

class AudiosTableTest extends TestCase {
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
        //Configure::write('Search.enabled', true);
        $this->Audio = TableRegistry::getTableLocator()->get('Audios');
        $this->AudioFixture =  new AudiosFixture();
    }

    function tearDown() {
        unset($this->Audio);
        parent::tearDown();
    }

    function _getRecord($record) {
        return $this->AudioFixture->records[$record];
    }

    function _saveRecordWith($record, $changedFields) {
        $data = $this->_getRecord($record);
        $this->Audio->deleteAll(array('1=1'));
        unset($data['id']);
        $data = array_merge($data, $changedFields);
        $audio = $this->Audio->newEntity($data);
        return (bool)$this->Audio->save($audio);
    }

    function _saveRecordWithout($record, $missingFields) {
        $data = $this->_getRecord($record);
        $this->Audio->deleteAll(array('1=1'));
        unset($data['id']);
        foreach ($missingFields as $field) {
            unset($data[$field]);
        }
        $audio = $this->Audio->newEntity($data);
        return (bool)$this->Audio->save($audio);
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
        $data = $this->Audio->get(1);
        $data->sentence_id = 10;
        
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
        $data = $this->Audio->get($audioId);
        $data->sentence_id = 7;
        $this->Audio->save($data);

        $expected = array(1, 2, 3, 4, 7);
        $result = $this->Audio->Sentences->ReindexFlags->find('all')
            ->order('sentence_id')
            ->toList();
        $result = Hash::extract($result, '{n}.sentence_id');
        $this->assertEquals($expected, $result);
    }

    function testSentencesReindexedOnCreate() {
        $expected = array(4, 6, 10);

        $this->_saveRecordWith(0, array('sentence_id' => 10));

        $result = $this->Audio->Sentences->ReindexFlags->find('all')
            ->order('sentence_id')
            ->toList();
        $result = Hash::extract($result, '{n}.sentence_id');
        $this->assertEquals($expected, $result);
    }

    function testSentencesReindexedOnDelete() {
        $expected = array(1, 2, 3, 4);

        $audio = $this->Audio->get(1);
        $this->Audio->delete($audio);

        $result = $this->Audio->Sentences->ReindexFlags->find('all')
            ->order('sentence_id')
            ->toList();
        $result = Hash::extract($result, '{n}.sentence_id');
        $this->assertEquals($expected, $result);
    }

    function testSphinxAttributesChanged_onUpdate() {
        $audioId = 1;
        $sentenceId = 3;
        $expectedAttributes = array('has_audio');
        $expectedValues = array(
            $sentenceId => array(1),
        );

        $this->Audio->sphinxAttributesChanged($attributes, $values, $isMVA, $sentenceId);

        $this->assertEquals($expectedAttributes, $attributes);
        $this->assertEquals($expectedValues, $values);
    }

    function testSphinxAttributesChanged_onDelete() {
        $audioId = 1;
        $sentenceId = 1;
        $expectedAttributes = array('has_audio');
        $expectedValues = array(
            $sentenceId => array(0),
        );

        $this->Audio->sphinxAttributesChanged($attributes, $values, $isMVA, $sentenceId);

        $this->assertEquals($expectedAttributes, $attributes);
        $this->assertEquals($expectedValues, $values);
    }

}
