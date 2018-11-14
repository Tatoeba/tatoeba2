<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SentencesTable;
use App\Behavior\Sphinx;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use App\Model\CurrentUser;

class SentencesTableTest extends TestCase {
	public $fixtures = array(
		'app.sentences',
		'app.users',
		'app.users_languages',
		'app.contributions',
		'app.sentences_lists',
		'app.sentences_sentences_lists',
		'app.tags',
		'app.tags_sentences',
		'app.languages',
		'app.links',
		'app.transcriptions',
		'app.reindex_flags',
		'app.audios',
		'app.users_sentences'
	);

	function setUp() {
		parent::setUp();
		Configure::write('Acl.database', 'test');
		$this->Sentence = TableRegistry::getTableLocator()->get('Sentences');

		/*
		$this->Sentence->Behaviors->Sphinx = Mockery::mock();

		Configure::write('AutoTranscriptions.enabled', true);
		$autotranscription = $this->_installAutotranscriptionMock();
		$autotranscription
			->expects($this->any())
			->method('cmn_detectScript')
			->will($this->returnValue('Hans'));
		$autotranscription
			->expects($this->any())
			->method('jpn_Jpan_to_Hrkt_generate')
			->will($this->returnValue('transcription in furigana'));
		$autotranscription
			->expects($this->any())
			->method('jpn_Jpan_to_Hrkt_validate')
			->will($this->returnValue(true));
		*/
	}

	function _installAutotranscriptionMock() {
		$autotranscription = $this->getMock('Autotranscription', array(
			'cmn_detectScript',
			'jpn_Jpan_to_Hrkt_generate',
			'jpn_Jpan_to_Hrkt_validate',
		));
		$this->Sentence->Transcription->setAutotranscription($autotranscription);
		return $autotranscription;
	}

	function tearDown() {
		unset($this->Sentence);
		parent::tearDown();
	}

	function beOwnerOfCurrentSentence($id) {
		$sentence = $this->Sentence->get($id);
		CurrentUser::store(array('id' => $sentence->user_id));
	}

	function testSave_firesEventOnUpdate() {
		$dispatched = false;
		$id = 1;
		$data = $this->Sentence->newEntity([
			'id' => $id,
			'text' => 'Changing text of sentence #1.',
		]);
		$model = $this->Sentence;
		$model->getEventManager()->attach(
			function (Event $event) use ($model, &$dispatched, $id, $data) {
				$this->assertSame($model->getAlias(), $event->getSubject()->getAlias());
				// filter out unpredictable keys like 'modified' => now()
				// from $event->data['data']
				$result = [
					'id' => $event->getData('id'),
					'created' => $event->getData('created'),
					'data' => $event->getData('data')
				];
				$created = $result['created'];
				$expectedEventData = compact('id', 'created', 'data');
				$this->assertEquals($expectedEventData, $result);
				$dispatched = true;
			},
			'Model.Sentence.saved'
		);

		$this->Sentence->save($data);

		$this->assertTrue($dispatched);
	}

	function testSaveNewSentence_addsOneSentence() {
		$oldNumberOfSentences = $this->Sentence->find('all')->count();
		$this->Sentence->saveNewSentence('Hello world.', 'eng', 1);
		$newNumberOfSentences = $this->Sentence->find('all')->count();
		$sentencesAdded = $newNumberOfSentences - $oldNumberOfSentences;

		$this->assertEquals($sentencesAdded, 1);
	}

	function testSaveNewSentence_nullifiesEmptyLangs() {
		$text = 'Hello world.';

		$this->Sentence->saveNewSentence($text, '', 1);
		$savedSentence = $this->Sentence->find('all')
			->where(['text' => $text])
			->first();
		
		$this->assertNull($savedSentence->lang);
	}

	function testSaveNewSentence_returnsTrueWhenSaved() {
		$returnValue = $this->Sentence->saveNewSentence('Hello world.', 'eng', 1);
		$this->assertTrue((bool)$returnValue);
	}

	function testSaveTranslation_links() {
		$user = $this->Sentence->User->findByUsername('kazuki');
		CurrentUser::store($user['User']);

		$translationFromSentenceId = 1;
		$lastId = $this->Sentence->find('first', array('fields' => array('MAX(id)+1 AS v')));
		$newlyCreatedSentenceId = $lastId[0]['v'];
		$this->Sentence->Link = $this->getMockForModel('Link', array('add'));

		$this->Sentence->Link
			->expects($this->once())
			->method('add')
			->with($translationFromSentenceId, $newlyCreatedSentenceId, 'eng', 'eng');

		$this->Sentence->saveTranslation(
			$translationFromSentenceId,
			'eng',
			'This is the translation.',
			'eng',
			Sentence::MAX_CORRECTNESS
		);
	}

	function testSave_validSentence() {
		$data = $this->Sentence->newEntity([
			'text' => 'Hi there!',
		]);
		
		$result = $this->Sentence->save($data);
		$this->assertTrue((bool)$result);
	}

	function testSave_checksValidLicense() {
		$data = $this->Sentence->newEntity([
			'text' => 'Trying to save a sentence with an invalid license.',
			'license' => 'some-strange-thing',
		]);
		$result = $this->Sentence->save($data);
		$this->assertFalse((bool)$result);
	}

	function testSave_checksLicenseUpdatesFine() {
		$this->beOwnerOfCurrentSentence(48);
		$data = $this->Sentence->get(48);
		$data->license = 'CC0 1.0';
		$result = $this->Sentence->save($data);

		$this->assertTrue((bool)$result);
	}

	function testSave_checksLicenseDoesntUpdateIfCurrentUserIsNotOwner() {
		CurrentUser::store(array('id' => 3));
		$data = $this->Sentence->get(48);
		$data = $this->Sentence->patchEntity($data, ['license' => 'CC0 1.0']);
		$result = $this->Sentence->save($data);
		$this->assertFalse((bool)$result);
	}

	function testSave_checksLicenseDoesntUpdateIfBasedOnIdIsNull() {
		$this->beOwnerOfCurrentSentence(1);
		$data = $this->Sentence->get(1);
		$data = $this->Sentence->patchEntity($data, ['license' => 'CC0 1.0']);
		$result = $this->Sentence->save($data);
		$this->assertFalse((bool)$result);
	}

	function testSave_checksLicenseDoesntUpdateIfAddedAsTranslation() {
		$this->beOwnerOfCurrentSentence(49);
		$data = $this->Sentence->get(49);
		$data = $this->Sentence->patchEntity($data, ['license' => 'CC0 1.0']);
		$result = $this->Sentence->save($data);
		$this->assertFalse((bool)$result);
	}

	function testSave_checksLicenseDoesntUpdateToAMoreRestrictiveLicense() {
		$this->beOwnerOfCurrentSentence(51);
		$data = $this->Sentence->get(51);
		$data = $this->Sentence->patchEntity($data, ['license' => 'CC BY 2.0 FR']);
		$result = $this->Sentence->save($data);
		$this->assertFalse((bool)$result);
	}

	function testSave_checksLicenseUpdatesFromNullLicense() {
		$this->beOwnerOfCurrentSentence(52);
		$data = $this->Sentence->get(52);
		$data = $this->Sentence->patchEntity($data, ['license' => 'CC BY 2.0 FR']);
		$result = $this->Sentence->save($data);
		$this->assertTrue((bool)$result);
	}

	function testSave_checksLicenseDoesntUpdateToTheSameLicense() {
		$this->beOwnerOfCurrentSentence(48);
		$data = $this->Sentence->get(48);
		$data = $this->Sentence->patchEntity($data, ['license' => 'CC BY 2.0 FR']);
		$result = $this->Sentence->save($data);
		$this->assertFalse((bool)$result);
	}

	function testSave_checksLicenseDoesntUpdateIfOwnerIsNotTheOriginalCreator() {
		$this->beOwnerOfCurrentSentence(50);
		$data = $this->Sentence->get(50);
		$data = $this->Sentence->patchEntity($data, ['license' => 'CC0 1.0']);
		$result = $this->Sentence->save($data);
		$this->assertFalse((bool)$result);
	}

	function testSave_setsDefaultLicenseSettingOnCreation() {
		$data = $this->Sentence->newEntity([
			'text' => 'This sentence should get a default licence.',
			'user_id' => 7,
		]);
		$savedSentence = $this->Sentence->save($data);
		$this->assertEquals('CC0 1.0', $savedSentence->license);
	}

	function testSave_doesNotChangeLicenseOnUpdate() {
		$data = $this->Sentence->newEntity([
			'id' => 1,
			'text' => 'Updating sentence #1.',
			'user_id' => 7,
		]);

		$savedSentence = $this->Sentence->save($data);
		$savedSentence = $this->Sentence->get($savedSentence->id);
		$this->assertEquals('CC BY 2.0 FR', $savedSentence->license);
	}

    function testSentenceAdditionAddsTranscription() {
        $result = $this->Sentence->save(array(
            'text' => '歌舞伎ってご存知ですか？',
            'lang' => 'jpn'
        ));
        $newSentence = $this->Sentence->getLastInsertID();
        $transcriptions = $this->Sentence->Transcription->find(
            'count',
            array('conditions' => array('sentence_id' => $newSentence))
        );
        $this->assertEquals(1, $transcriptions);
    }

	function testSentenceTextEditionUpdatesScript() {
		$autotranscription = $this->_installAutotranscriptionMock();
		$autotranscription
			->expects($this->once())
			->method('cmn_detectScript')
			->will($this->returnValue('Hant'));
		$cmnSentenceId = 2;
		$this->Sentence->id = $cmnSentenceId;
		$this->Sentence->save(array(
			'text' => '問題的根源是，在當今世界，愚人充滿了自信，而智者充滿了懷疑。',
		));
		$result = $this->Sentence->findById($cmnSentenceId, 'script');
		$this->assertEquals('Hant', $result['Sentence']['script']);
	}

	function testSentenceFlagEditionUpdatesScript() {
		$cmnSentenceId = 2;
		$this->Sentence->save(array(
			'id' => $cmnSentenceId,
			'lang' => 'eng',
		));
		$result = $this->Sentence->findById($cmnSentenceId, 'script');
		$this->assertNull($result['Sentence']['script']);
	}

	function testSentenceTextEditionRegeneratesTranscriptions() {
		$jpnSentenceId = 6;
		$fieldsDiffers = array('text');
		$conditions = array('sentence_id' => $jpnSentenceId);
		$transcrBefore = $this->Sentence->Transcription->find(
			'all', compact('fields', 'conditions')
		);

		$this->Sentence->id = $jpnSentenceId;
		$this->Sentence->save(array(
			'text' => '未来から来ました。',
		));

		$transcrAfter = $this->Sentence->Transcription->find(
			'all', compact('fields', 'conditions')
		);
		$this->assertEquals(count($transcrBefore), count($transcrAfter));
		$this->assertNotEquals($transcrBefore, $transcrAfter);
	}

	function testSentenceFlagEditionGeneratesTranscriptions() {
		$engSentenceId = 1;
		$conditions = array('sentence_id' => $engSentenceId);

		$this->Sentence->id = $engSentenceId;
		$this->Sentence->save(array(
			'lang' => 'jpn',
		));

		$nbTranscr = $this->Sentence->Transcription->find(
			'count', compact('conditions')
		);
		$this->assertTrue($nbTranscr > 0);
	}

	function testSentenceFlagEditionDeletesTranscriptions() {
		$jpnSentenceId = 6;
		$conditions = array('sentence_id' => $jpnSentenceId);

		$this->Sentence->id = $jpnSentenceId;
		$this->Sentence->save(array(
			'lang' => 'deu',
		));

		$nbTranscr = $this->Sentence->Transcription->find(
			'count', compact('conditions')
		);
		$this->assertTrue($nbTranscr == 0);
	}

	function testSentenceUnadoptionDoesntTouchTranscriptions() {
		$jpnSentenceId = 6;
		$jpnSentenceOwner = 7;
		$conditions = array('sentence_id' => $jpnSentenceId);
		$transcrBefore = $this->Sentence->Transcription->find(
			'all', compact('conditions')
		);

		$this->Sentence->unsetOwner($jpnSentenceId, $jpnSentenceOwner);

		$transcrAfter = $this->Sentence->Transcription->find(
			'all', compact('conditions')
		);
		$this->assertEquals($transcrBefore, $transcrAfter);
	}

	function testSentenceDeletionDeletesTranscriptions() {
		$jpnSentenceId = 6;
		$this->Sentence->delete($jpnSentenceId, false);

		$transcr = $this->Sentence->Transcription->find('all', array(
			'conditions' => array('sentence_id' => $jpnSentenceId),
		));
		$this->assertEquals(array(), $transcr);
	}

	function testGetSentencesLang_returnsLang() {
		$result = $this->Sentence->getSentencesLang(array(3, 4, 8));
		$expectedLangs = array(3 => 'spa', 4 => 'fra', 8 => 'fra');
		$this->assertEquals($expectedLangs, $result);
	}

	function testGetSentencesLang_returnsNullForFlaglessSentences() {
		$result = $this->Sentence->getSentencesLang(array(9));
		$expectedLangs = array(9 => null);
		$this->assertEquals($expectedLangs, $result);
	}

	function testSentenceRemovedOnDelete() {
		$sentenceId = 1;

		$this->Sentence->delete($sentenceId, false);

		$result = (bool)$this->Sentence->findById($sentenceId);
		$this->assertFalse($result);
	}

	function testReturnsTrueOnDelete() {
		$sentenceId = 1;

		$result = $this->Sentence->delete($sentenceId, false);

		$this->assertTrue($result);
	}

	function testReturnsFalseIfAudioOnDelete() {
		$sentenceId = 3;

		$result = $this->Sentence->delete($sentenceId, false);

		$this->assertFalse($result);
	}

	function testTranslationLinksFromSentenceRemovedOnDelete() {
		$sentenceId = 1;

		$this->Sentence->delete($sentenceId, false);

		$trans = $this->Sentence->Link->findDirectTranslationsIds($sentenceId);
		$this->assertEquals(array(), $trans);
	}

	function testLogsSentenceDeletionOnDelete() {
		$sentenceId = 1;
		$conditions = array('type' => 'sentence');
		$before = $this->Sentence->Contribution->find('count', compact('conditions'));

		$this->Sentence->delete($sentenceId, false);

		$after = $this->Sentence->Contribution->find('count', compact('conditions'));
		$added = $after - $before;
		$this->assertEquals(1, $added);
	}

	function testLogsSentenceDeletionWithFieldsOnDelete() {
		$sentenceId = 1;
		$sentence = $this->Sentence->findById($sentenceId);
		$expected = array(
			'sentence_id' => $sentenceId,
			'sentence_lang' => $sentence['Sentence']['lang'],
			'text' => $sentence['Sentence']['text'],
			'action' => 'delete',
		);
		$fields = array('sentence_id', 'sentence_lang', 'text', 'action');
		$conditions = array('type' => 'sentence');
		$before = $this->Sentence->Contribution->deleteAll('1=1');

		$this->Sentence->delete($sentenceId, false);

		$log = $this->Sentence->Contribution->find('all',
			compact('conditions', 'fields')
		);
		$this->assertEquals($expected, $log[0]['Contribution']);
	}

	function testLogsLinkDeletionOnDelete() {
		$sentenceId = 5;
		$conditions = array('type' => 'link');
		$before = $this->Sentence->Contribution->find('count', compact('conditions'));

		$this->Sentence->delete($sentenceId, false);

		$after = $this->Sentence->Contribution->find('count', compact('conditions'));
		$added = $after - $before;
		$this->assertEquals(2, $added);
	}

	function testLogsLinkDeletionWithFieldsOnDelete() {
		$sentenceId = 1;
		$expected = array(
			array('Contribution' => array(
				'sentence_id' => $sentenceId,
				'translation_id' => 2,
			)),
			array('Contribution' => array(
				'sentence_id' => 2,
				'translation_id' => $sentenceId,
			)),
			array('Contribution' => array(
				'sentence_id' => $sentenceId,
				'translation_id' => 3,
			)),
			array('Contribution' => array(
				'sentence_id' => 3,
				'translation_id' => $sentenceId,
			)),
			array('Contribution' => array(
				'sentence_id' => $sentenceId,
				'translation_id' => 4,
			)),
			array('Contribution' => array(
				'sentence_id' => 4,
				'translation_id' => $sentenceId,
			)),
		);
		$conditions = array('type' => 'link');
		$contain = array();
		$fields = array('sentence_id', 'translation_id');
		$before = $this->Sentence->Contribution->deleteAll('1=1');

		$this->Sentence->delete($sentenceId, false);

		$logs = $this->Sentence->Contribution->find('all',
			compact('conditions', 'fields', 'contain')
		);
		$this->assertEquals($expected, $logs);
	}

	function testTranslationLinksToSentenceRemovedOnDelete() {
		$sentenceId = 1;

		$translations = $this->Sentence->Link->findDirectTranslationsIds($sentenceId);

		$this->Sentence->delete($sentenceId, false);

		foreach($translations as $transId) {
			$trans = $this->Sentence->Link->findDirectTranslationsIds($transId);
			$this->assertFalse(in_array($sentenceId, $trans));
		}
	}

	function testLanguageCountDecrementedOnDelete() {
		$sentenceId = 1;
		$sentence = $this->Sentence->findById($sentenceId, 'lang');
		$language = $this->Sentence->Language->findByCode($sentence['Sentence']['lang'], 'sentences');
		$countBefore = $language['Language']['sentences'];

		$this->Sentence->delete($sentenceId, false);

		$language = $this->Sentence->Language->findByCode($sentence['Sentence']['lang'], 'sentences');
		$countAfter = $language['Language']['sentences'];
		$delta = $countAfter - $countBefore;
		$this->assertEquals(-1, $delta);
	}

	function testListsCleanedOnDelete() {
		$sentenceId = 8;
		$inListBefore = $this->Sentence->SentencesList->SentencesSentencesLists->findAllBySentenceId($sentenceId);

		$this->Sentence->delete($sentenceId, false);

		$inListAfter = $this->Sentence->SentencesList->SentencesSentencesLists->findAllBySentenceId($sentenceId);
		$delta = count($inListAfter) - count($inListBefore);
		$this->assertEquals(-1, $delta);
	}

	function testTagsAreRemovedOnDelete() {
		$sentenceId = 8;
		$tagsBefore = $this->Sentence->TagsSentences->getAllTagsOnSentence($sentenceId);

		$this->Sentence->delete($sentenceId, false);

		$tagsAfter = $this->Sentence->TagsSentences->getAllTagsOnSentence($sentenceId);
		$this->assertNotEquals(0, count($tagsBefore));
		$this->assertEquals(0, count($tagsAfter));
	}

	function testScriptIsSetOnSentenceCreation() {
		$cmnSentence = array(
			'lang' => 'cmn',
			'text' => '我们试试看！',
		);

		$this->Sentence->save($cmnSentence);

		$id = $this->Sentence->getLastInsertID();
		$savedSentence = $this->Sentence->findById($id, 'script');
		$this->assertEquals('Hans', $savedSentence['Sentence']['script']);
	}

	function testScriptIsNotSetOnSentenceCreation() {
		$cmnSentence = array(
			'lang' => 'eng',
			'text' => 'Who needs to specify script in English?',
		);

		$this->Sentence->save($cmnSentence);

		$id = $this->Sentence->getLastInsertID();
		$savedSentence = $this->Sentence->findById($id, 'script');
		$this->assertNull($savedSentence['Sentence']['script']);
	}

	function testScriptShouldBeValidOnUpdate() {
		$cmnSentence = array(
			'id' => 2,
			'script' => 'invalid script code!',
		);

		$result = $this->Sentence->save($cmnSentence);
		$this->assertFalse($result);
	}

	function testScriptShouldBeValidOnCreate() {
		$cmnSentence = array(
			'script' => 'invalid script code!',
			'lang' => 'cmn',
			'text' => '我们试试看！',
		);

		$result = $this->Sentence->save($cmnSentence);
		$this->assertFalse($result);
	}

	function testScriptShouldBeValidAndCheckType() {
		$cmnSentence = array(
			'script' => true,
			'lang' => 'cmn',
			'text' => '我们试试看！',
		);

		$result = $this->Sentence->save($cmnSentence);
		$this->assertFalse($result);
	}

	function testNeedsReindex() {
		$reindex = array(2, 3);
		$this->Sentence->needsReindex($reindex);
		$result = $this->Sentence->ReindexFlag->findAllBySentenceId($reindex);
		$this->assertEquals(2, count($result));
	}

	function testModifiedSentenceNeedsReindex() {
		$id = 1;
		$this->Sentence->id = $id;
		$this->Sentence->save(array('text' => 'Changed!'));
		$result = $this->Sentence->ReindexFlag->findBySentenceId($id);
		$this->assertTrue((bool)$result);
	}

	function testModifiedSentenceNeedsTranslationsReindex() {
		$expected = array(1, 2, 4, 5);
		$this->Sentence->id = 5;
		$this->Sentence->save(array('user_id' => 0));
		$result = $this->Sentence->ReindexFlag->find('all', array(
			'order' => 'sentence_id'
		));
		$result = Set::classicExtract($result, '{n}.ReindexFlag.sentence_id');
		$this->assertEquals($expected, $result);
	}

	function testRemovedSentenceNeedsItselfAndTranslationsReindex() {
		$expected = array(1, 2, 4, 5);
		$this->Sentence->delete(5, false);
		$result = $this->Sentence->ReindexFlag->find('all', array(
			'order' => 'sentence_id'
		));
		$result = Set::classicExtract($result, '{n}.ReindexFlag.sentence_id');
		$this->assertEquals($expected, $result);
	}

	function testSentenceLoosesOKTagOnEdition() {
		$sentenceId = 2;
		$OKTagId = $this->Sentence->Tag->getIdFromName(
			$this->Sentence->Tag->getOKTagName()
		);
		$this->assertTrue(
			$this->Sentence->TagsSentences->isSentenceTagged($sentenceId, $OKTagId)
		);
		$this->Sentence->save(array(
			'id' => $sentenceId,
			'lang' => 'cmn',
			'text' => "That should remove the OK tag automatically!"
		));
		$this->assertFalse(
			$this->Sentence->TagsSentences->isSentenceTagged($sentenceId, $OKTagId)
		);
	}

	function testSentenceDontLoosesOKTagOnFlagChange() {
		$sentenceId = 2;
		$OKTagId = $this->Sentence->Tag->getIdFromName(
			$this->Sentence->Tag->getOKTagName()
		);
		$this->assertTrue(
			$this->Sentence->TagsSentences->isSentenceTagged($sentenceId, $OKTagId)
		);
		$this->Sentence->save(array(
			'id' => $sentenceId,
			'lang' => 'ita',
		));
		$this->assertTrue(
			$this->Sentence->TagsSentences->isSentenceTagged($sentenceId, $OKTagId)
		);
	}

	function testSphinxAttributesChanged_onLetGo() {
		$sentenceId = 1;
		$expectedAttributes = array('user_id');
		$expectedValues = array(
			$sentenceId => array(0),
		);

		$this->Sentence->id = $sentenceId;
		$this->Sentence->data['Sentence'] = array(
			'id' => $sentenceId,
			'user_id' => null,
		);
		$this->Sentence->sphinxAttributesChanged($attributes, $values, $isMVA);

		$this->assertEquals($expectedAttributes, $attributes);
		$this->assertEquals($expectedValues, $values);
	}

	function testSphinxAttributesChanged_onOwn() {
		$sentenceId = 1;
		$ownerId = 42;
		$expectedAttributes = array('user_id');
		$expectedValues = array(
			$sentenceId => array($ownerId),
		);

		$this->Sentence->id = $sentenceId;
		$this->Sentence->data['Sentence'] = array(
			'id' => $sentenceId,
			'user_id' => $ownerId,
		);
		$this->Sentence->sphinxAttributesChanged($attributes, $values, $isMVA);

		$this->assertEquals($expectedAttributes, $attributes);
		$this->assertEquals($expectedValues, $values);
	}

	function testSphinxAttributesChanged_correctness() {
		$sentenceId = 1;
		$correctness = -1;
		$expectedAttributes = array('ucorrectness');
		$expectedValues = array(
			$sentenceId => array($correctness + 128),
		);

		$this->Sentence->id = $sentenceId;
		$this->Sentence->data['Sentence'] = array(
			'id' => $sentenceId,
			'correctness' => $correctness,
		);
		$this->Sentence->sphinxAttributesChanged($attributes, $values, $isMVA);

		$this->assertEquals($expectedAttributes, $attributes);
		$this->assertEquals($expectedValues, $values);
	}

	function testEditSentence_succeeds() {
		$user = $this->Sentence->User->findById(2);
		CurrentUser::store($user['User']);

		$data = array(
			'id' => 'eng_53',
			'value' => 'Edited sentence.'
		);
		$sentence = $this->Sentence->editSentence($data);

		$expected = array(
			'id' => 53,
			'lang' => 'eng',
			'text' => 'Edited sentence.',
			'hash' => '1kqlcvr'
		);
		$result = array_intersect_key($sentence['Sentence'], $expected);

		$this->assertEquals($expected, $result);
	}

	function testEditSentence_failsBecauseHasAudio() {
		$user = $this->Sentence->User->findById(7);
		CurrentUser::store($user['User']);

		$data = array(
			'id' => 'spa_3',
			'value' => 'changing'
		);
		$result = $this->Sentence->editSentence($data);
		$expected = $this->Sentence->findById(3);
		
		$this->assertEquals($expected, $result);
	}

	function testEditSentence_failsBecauseNotOwner() {
		$user = $this->Sentence->User->findById(4);
		CurrentUser::store($user['User']);

		$data = array(
			'id' => 'eng_1',
			'value' => 'Edited sentence.'
		);
		$result = $this->Sentence->editSentence($data);
		$expected = $this->Sentence->findById(1);

		$this->assertEquals($expected, $result);
	}

	function testEditSentence_failsBecauseWrongId() {
		$user = $this->Sentence->User->findById(4);
		CurrentUser::store($user['User']);
		
		$data = array(
			'id' => '53_eng',
			'value' => 'Edited sentence.'
		);
		$result = $this->Sentence->editSentence($data);
		
		$this->assertEmpty($result);
	}

	function testDeleteSentence_succeeds()
	{
		$user = $this->Sentence->User->findById(4);
		CurrentUser::store($user['User']);

		$result = $this->Sentence->deleteSentence(53);

		$this->assertTrue($result);
	}

	function testDeleteSentence_fails()
	{
		$user = $this->Sentence->User->findById(4);
		CurrentUser::store($user['User']);

		$result = $this->Sentence->deleteSentence(52);

		$this->assertFalse($result);
	}
}
