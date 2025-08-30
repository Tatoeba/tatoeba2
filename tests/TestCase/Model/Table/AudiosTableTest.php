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
        'app.disabled_audios',
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

    function testAssignAuthor_user() {
        $audio = $this->Audio->newEntity(['sentence_id' => 1]);
        $this->Audio->assignAuthor($audio, 'admin');
        $result = $this->Audio->save($audio);

        $expected = [
            'external' => null,
            'user_id' => 1
        ];
        $result = array_intersect_key($result->toArray(), $expected);
        $this->assertEquals($expected, $result);
    }

    function testAssignAuthor_external() {
        $audio = $this->Audio->newEntity(['sentence_id' => 1]);
        $this->Audio->assignAuthor($audio, 'Barack Obama');
        $result = $this->Audio->save($audio);

        $expected = [
            'external' => [
                'username' => 'Barack Obama',
                'license' => null,
                'attribution_url' => null,
            ],
            'user_id' => null,
        ];
        $result = array_intersect_key($result->toArray(), $expected);
        $this->assertEquals($expected, $result);
    }

    function testAssignAuthor_external_fails() {
        $audio = $this->Audio->newEntity(['sentence_id' => 1]);
        $this->Audio->assignAuthor($audio, 'Barack Obama', false);
        $result = $this->Audio->save($audio);
        $this->assertFalse($result);
    }

    function testNewAudio_incrementsCount() {
        $Languages = TableRegistry::getTableLocator()->get('Languages');
        $before = $Languages->find()->where(['code' => 'eng'])->first()->audio;

        $audio = $this->Audio->newEntity(['sentence_id' => 1]);
        $this->Audio->assignAuthor($audio, 'admin');
        $this->Audio->save($audio);

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

        $audio = $this->Audio->newEntity(['sentence_id' => 2]);
        $this->Audio->assignAuthor($audio, 'contributor');
        $added = $this->Audio->save($audio);

        $returned = $this->Audio->get($added->id);
        $this->assertEquals($added->created->format('Y-m-d H:i:s'), $returned->created->format('Y-m-d H:i:s'));
        $this->assertEquals($added->modified->format('Y-m-d H:i:s'), $returned->modified->format('Y-m-d H:i:s'));

        I18n::setLocale($prevLocale);
    }

    function testEdit_enable_ok() {
        $audio = $this->Audio->get(1);
        $this->assertTrue($audio->enabled);

        $this->Audio->edit($audio, ['enabled' => false]);
        $this->Audio->save($audio);

        try {
            $this->Audio->get(1);
            $result = true;
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $result = false;
        }

        $this->assertFalse($result);
        $DisabledAudios = TableRegistry::getTableLocator()->get('DisabledAudios');
        $this->assertFalse($DisabledAudios->get(1)->enabled);
    }

    function testEdit_enable_fails() {
        $audio = $this->Audio->get(1);
        $this->assertTrue($audio->enabled);

        $this->Audio->edit($audio, ['enabled' => 'invalid data here']);
        $this->Audio->save($audio);

        $this->assertTrue($audio->hasErrors());
        $this->assertTrue($this->Audio->get(1)->enabled);
    }

    function testEdit_change_author_ok() {
        $audio = $this->Audio->get(1);
        $this->assertEquals(4, $audio->user_id);

        $this->Audio->edit($audio, ['author' => 'admin']);
        $this->Audio->save($audio);

        $this->assertEquals(1, $this->Audio->get(1)->user_id);
    }

    function testEdit_change_author_empty_ok() {
        $audio = $this->Audio->get(1);
        $this->assertEquals(4, $audio->user_id);

        $this->Audio->edit($audio, ['author' => '']);
        $this->Audio->save($audio);

        $this->assertEquals(4, $this->Audio->get(1)->user_id);
    }

    function testEdit_change_external_author_ok() {
        $audio = $this->Audio->get(1);
        $this->assertEquals(4, $audio->user_id);
        $this->assertNull($audio->external);

        $this->Audio->edit($audio, ['author' => 'Barack Obama']);
        $this->Audio->save($audio);

        $audio = $this->Audio->get(1);
        $this->assertNull($audio->user_id);
        $this->assertEquals('Barack Obama', $audio->external['username']);
    }

    function testSentencesFinder() {
        $result = $this->Audio->find('sentences')->all()->toList();

        $this->assertEquals(5, count($result));

        $this->assertEquals(57, $result[0]->id);
        $this->assertEquals(1, count($result[0]->audios));

        $this->assertEquals(15, $result[1]->id);
        $this->assertEquals(1, count($result[1]->audios));

        $this->assertEquals(4, $result[2]->id);
        $this->assertEquals(2, count($result[2]->audios));

        $this->assertEquals(12, $result[3]->id);
        $this->assertEquals(1, count($result[3]->audios));

        $this->assertEquals(3, $result[4]->id);
        $this->assertEquals(1, count($result[4]->audios));
    }

    function testSentencesFinder_maxResults() {
        $result = $this->Audio->find('sentences', ['maxResults' => 5])->all()->toList();

        $this->assertEquals(4, count($result));

        $this->assertEquals(57, $result[0]->id);
        $this->assertEquals(1, count($result[0]->audios));

        $this->assertEquals(15, $result[1]->id);
        $this->assertEquals(1, count($result[1]->audios));

        $this->assertEquals(4, $result[2]->id);
        $this->assertEquals(2, count($result[2]->audios));

        $this->assertEquals(12, $result[3]->id);
        $this->assertEquals(1, count($result[3]->audios));
    }

    function testSentencesFinder_lang() {
        $result = $this->Audio->find('sentences', ['lang' => 'fra'])->all()->toList();

        $this->assertEquals(2, count($result));

        $this->assertEquals(4, $result[0]->id);
        $this->assertEquals(2, count($result[0]->audios));

        $this->assertEquals(12, $result[1]->id);
        $this->assertEquals(1, count($result[1]->audios));
    }

    function testSentencesFinder_lang_maxResults() {
        $result = $this->Audio->find('sentences', ['lang' => 'fra', 'maxResults' => 1])->all()->toList();

        $this->assertEquals(1, count($result));

        $this->assertEquals(4, $result[0]->id);
        $this->assertEquals(2, count($result[0]->audios));
    }

    function testSentencesFinder_user_id() {
        $result = $this->Audio->find('sentences', ['user_id' => 3])->all()->toList();

        $this->assertEquals(2, count($result));

        $this->assertEquals(15, $result[0]->id);
        $this->assertEquals(1, count($result[0]->audios));
        $this->assertEquals(3, $result[0]->audios[0]->user_id);

        $this->assertEquals(4, $result[1]->id);
        $this->assertEquals(1, count($result[1]->audios));
        $this->assertEquals(3, $result[1]->audios[0]->user_id);
    }

    function testSentencesFinder_user_id_maxResults() {
        $result = $this->Audio->find('sentences', ['user_id' => 3, 'maxResults' => 1])->all()->toList();

        $this->assertEquals(1, count($result));

        $this->assertEquals(15, $result[0]->id);
        $this->assertEquals(1, count($result[0]->audios));
        $this->assertEquals(3, $result[0]->audios[0]->user_id);
    }

    function testSentencesCountFinder() {
        $result = $this->Audio->find('sentencesCount');

        $this->assertEquals(5, $result);
    }

    function testSentencesCountFinder_withLang() {
        $result = $this->Audio->find('sentencesCount', ['lang' => 'fra']);

        $this->assertEquals(2, $result);
    }

    function testChangeSentenceLangChangesAudioSentenceLang() {
        $DisabledAudios = TableRegistry::getTableLocator()->get('DisabledAudios');
        $sentenceId = 3;

        $before = $this->Audio->findBySentenceId($sentenceId)->all()->toList();
        $disabledBefore = $DisabledAudios->findBySentenceId($sentenceId)->all()->toList();

        $sentence = $this->Audio->Sentences->get($sentenceId);
        $sentence->lang = 'hun';
        $this->Audio->Sentences->save($sentence);

        $after = $this->Audio->findBySentenceId($sentenceId)->all()->toList();
        $disabledAfter = $DisabledAudios->findBySentenceId($sentenceId)->all()->toList();

        $this->assertEquals(1, count($before));
        $this->assertEquals(1, count($disabledBefore));
        $this->assertEquals(1, count($after));
        $this->assertEquals(1, count($disabledAfter));

        $this->assertEquals('spa', $before[0]->sentence_lang);
        $this->assertEquals('spa', $disabledBefore[0]->sentence_lang);
        $this->assertEquals('hun', $after[0]->sentence_lang);
        $this->assertEquals('hun', $disabledAfter[0]->sentence_lang);
    }

    function testCreatingAudioSetsSentenceLang() {
        $sentence_id = 2;
        $audio = $this->Audio->newEntity(compact('sentence_id'));
        $this->Audio->assignAuthor($audio, 'contributor');
        $result = $this->Audio->save($audio);

        $this->assertEquals('cmn', $this->Audio->get($result->id)->sentence_lang);
    }

    function testGetAudio_virtualFields_hasUserWithAudioAttribution() {
        $audio = $this->Audio->findById(1)->contain(['Users'])->first();

        $this->assertEquals('https://example.com/my-audios', $audio->attribution_url);
        $this->assertEquals('CC BY 4.0', $audio->license);
        $this->assertEquals('contributor', $audio->author);
    }

    function testGetAudio_virtualFields_hasUserWithoutAudioAttribution() {
        $audio = $this->Audio->findById(5)->contain(['Users'])->first();

        $this->assertEquals('/user/profile/advanced_contributor', $audio->attribution_url);
        $this->assertEquals('', $audio->license);
        $this->assertEquals('advanced_contributor', $audio->author);
    }

    function testGetAudio_virtualFields_hasExternalUserWithAudioAttribution() {
        $audio = $this->Audio->findById(3)->contain(['Users'])->first();

        $this->assertEquals('https://example.fr/petit', $audio->attribution_url);
        $this->assertEquals('CC BY-NC 4.0', $audio->license);
        $this->assertEquals('Philippe Petit', $audio->author);
    }

    function testGetAudio_virtualFields_hasExternalUserWithoutAudioAttribution() {
        $audio = $this->Audio->findById(2)->contain(['Users'])->first();

        $this->assertEquals('', $audio->attribution_url);
        $this->assertEquals('', $audio->license);
        $this->assertEquals('Philippe Petit', $audio->author);
    }
}
