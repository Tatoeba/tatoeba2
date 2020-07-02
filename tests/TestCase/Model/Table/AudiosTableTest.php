<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AudiosTable;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use App\Test\Fixture\AudiosFixture;
use Cake\Utility\Hash;
use Cake\I18n\I18n;

class AudiosTableTest extends TestCase {
    public $fixtures = array(
        'app.audios',
        'app.contributions',
        'app.favorites_users',
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
        $entity = $this->Audio->get(1);
        $sentenceId = $entity->sentence_id;
        $expectedAttributes = array('has_audio');
        $expectedValues = array(
            $sentenceId => array(1),
        );

        $this->Audio->sphinxAttributesChanged($attributes, $values, $isMVA, $entity);

        $this->assertEquals($expectedAttributes, $attributes);
        $this->assertEquals($expectedValues, $values);
    }

    function testSphinxAttributesChanged_onDelete() {
        $entity = $this->Audio->get(1);
        $sentenceId = $entity->sentence_id;
        $expectedAttributes = array('has_audio');
        $expectedValues = array(
            $sentenceId => array(0),
        );

        $this->Audio->delete($entity);
        $this->Audio->sphinxAttributesChanged($attributes, $values, $isMVA, $entity);

        $this->assertEquals($expectedAttributes, $attributes);
        $this->assertEquals($expectedValues, $values);
    }

    function testExternalFieldParsedAsJSON()
    {
        $audio = $this->Audio->get(2);

        $this->assertEquals('Philippe Petit', $audio->external['username']);
    }

    function testNumberOfAudiosBy() {
        $result = $this->Audio->numberOfAudiosBy(4);
        $this->assertEquals(1, $result);
    }

    function testAssignAudioTo() {
        $result = $this->Audio->assignAudioTo(1, 'admin');
        $expected = [
            'sentence_id' => 1,
            'user_id' => 1
        ];
        $result = array_intersect_key($result->toArray(), $expected);
        $this->assertEquals($expected, $result);
    }

    function testAssignAudioTo_incrementsCount() {
        $Languages = TableRegistry::getTableLocator()->get('Languages');
        $before = $Languages->find()->where(['code' => 'eng'])->first()->audio;
        $result = $this->Audio->assignAudioTo(1, 'admin');
        $after= $Languages->find()->where(['code' => 'eng'])->first()->audio;
        $this->assertEquals(1, $after - $before);
    }

    function testDelete_decrementsCount() {
        $Languages = TableRegistry::getTableLocator()->get('Languages');
        $before = $Languages->find()->where(['code' => 'fra'])->first()->audio;
        $audioToDelete = $this->Audio->get(2);
        $result = $this->Audio->delete($audioToDelete);
        $after = $Languages->find()->where(['code' => 'fra'])->first()->audio;
        $this->assertEquals(1, $before - $after);
    }

    function testAssignAudioTo_correctDateUsingArabicLocale() {
        $prevLocale = I18n::getLocale();
        I18n::setLocale('ar');

        $added = $this->Audio->assignAudioTo(2, 'contributor');
        $returned = $this->Audio->get($added->id);
        $this->assertEquals($added->created, $returned->created);
        $this->assertEquals($added->modified, $returned->modified);

        I18n::setLocale($prevLocale);
    }
}
