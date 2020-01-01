<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SentencesTable;
use App\Behavior\Sphinx;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use App\Model\CurrentUser;
use App\Lib\Autotranscription;
use Cake\Datasource\Exception\RecordNotFoundException;
use App\Model\Entity\Contribution;
use App\Model\Entity\User;
use Cake\Utility\Hash;
use Cake\I18n\I18n;

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
		'app.users_sentences',
		'app.favorites_users',
	);

	function setUp() {
		parent::setUp();
		Configure::write('AutoTranscriptions.enabled', true);
		$this->Sentence = TableRegistry::getTableLocator()->get('Sentences');
		$autotranscription = $this->_installAutotranscriptionMock();
		$autotranscription
			->expects($this->any())
			->method('cmn_detectScript')
			->will($this->returnValue('Hans'));
		$autotranscription
			->expects($this->any())
			->method('jpn_Jpan_to_Hrkt_generate')
			->with($this->logicalNot($this->isEmpty()), $this->anything())
			->will($this->returnValue('transcription in furigana'));
		$autotranscription
			->expects($this->any())
			->method('jpn_Jpan_to_Hrkt_validate')
			->will($this->returnValue(true));		
	}

	function _installAutotranscriptionMock() {
		$autotranscription = $this->getMockBuilder(Autotranscription::class)
			->setMethods([
				'cmn_detectScript',
				'jpn_Jpan_to_Hrkt_generate',
				'jpn_Jpan_to_Hrkt_validate',
			])
			->getMock();
		
		$this->Sentence->Transcriptions->setAutotranscription($autotranscription);
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
		$model->getEventManager()->on(
			'Model.Sentence.saved',
			function (Event $event) use ($model, &$dispatched, $id, $data) {
				$this->assertSame($model, $event->getSubject());
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
			}
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

	function testSaveNewSentence_doesntAddDuplicate() {
		$sentence = $this->Sentence->saveNewSentence('What are you doing?', 'eng', 1);
		$this->assertEquals(27, $sentence->id);
	}

	function testSaveTranslation_links() {
		CurrentUser::store(['id' => 7]);

		$translationFromSentenceId = 1;
		$lastSentence = $this->Sentence->find()->select(['max' => 'MAX(id)'])->first();
		$newlyCreatedSentenceId = $lastSentence->max + 1;

		$this->Sentence->Links = $this->getMockBuilder(LinksTable::class)
			->setMethods(['add', 'findDirectAndIndirectTranslationsIds'])
			->getMock();
			
		$this->Sentence->Links
			->expects($this->once())
			->method('add')
			->with($translationFromSentenceId, $newlyCreatedSentenceId, 'eng', 'eng');	

		$translation = $this->Sentence->saveTranslation(
			$translationFromSentenceId,
			'eng',
			'This is the translation.',
			'eng'
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
		$data = $this->Sentence->patchEntity($data, ['license' => 'CC0 1.0']);
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

	function testSave_checksLicenseDoesUpdateIfAdmin() {
		CurrentUser::store($this->Sentence->Users->get(1));
		$data = $this->Sentence->get(48);
		$data = $this->Sentence->patchEntity($data, ['license' => 'CC0 1.0']);
		$result = $this->Sentence->save($data);
		$this->assertTrue((bool)$result);
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

	function testSave_checksLicenseBypassValidationIfAdmin() {
		CurrentUser::store($this->Sentence->Users->get(1));
		$data = $this->Sentence->get(50);
		$data = $this->Sentence->patchEntity($data, ['license' => 'CC0 1.0']);
		$result = $this->Sentence->save($data);
		$this->assertTrue((bool)$result);
	}

	function testSave_checksLicenseDoesntUpdateToInvalidLicense() {
		CurrentUser::store($this->Sentence->Users->get(1));
		$data = $this->Sentence->get(50);
		$data = $this->Sentence->patchEntity($data, ['license' => 'CL42 Crazy License']);
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
		$data = $this->Sentence->newEntity([
			'text' => '歌舞伎ってご存知ですか？',
            'lang' => 'jpn'
		]);
        $newSentence = $this->Sentence->save($data);
		$transcriptions = $this->Sentence->Transcriptions->find()
			->where(['sentence_id' => $newSentence->id])
			->count();
        $this->assertEquals(1, $transcriptions);
    }

	function testSentenceTextEditionUpdatesScript() {
		$autotranscription = $this->_installAutotranscriptionMock();
		$autotranscription
			->expects($this->once())
			->method('cmn_detectScript')
			->will($this->returnValue('Hant'));
		$cmnSentenceId = 2;
		$data = $this->Sentence->get($cmnSentenceId);
		$data->text = '問題的根源是，在當今世界，愚人充滿了自信，而智者充滿了懷疑。';
		$this->Sentence->save($data);
		$result = $this->Sentence->get($cmnSentenceId);
		$this->assertEquals('Hant', $result->script);
	}

	function testSentenceFlagEditionUpdatesScript() {
		$cmnSentenceId = 2;
		$user = $this->Sentence->Users->get(1);
		CurrentUser::store($user);

		$this->Sentence->changeLanguage($cmnSentenceId, 'eng');

		$result = $this->Sentence->get($cmnSentenceId, ['fields' => ['script']]);
		$this->assertNull($result->script);
	}

	function testSentenceTextEditionRegeneratesTranscriptions() {
		$jpnSentenceId = 6;
		$conditions = array('sentence_id' => $jpnSentenceId);
		$transcrBefore = $this->Sentence->Transcriptions->find('all')
			->where($conditions)
			->first();
		$data = $this->Sentence->get($jpnSentenceId);
		$data->text = '未来から来ました。';
		$this->Sentence->save($data);

		$transcrAfter = $this->Sentence->Transcriptions->find('all')
			->where($conditions)
			->first();

		$this->assertEquals(count($transcrBefore), count($transcrAfter));
		$this->assertNotEquals($transcrBefore, $transcrAfter);
	}

	function testSentenceFlagEditionGeneratesTranscriptions() {
		$user = $this->Sentence->Users->get(1);
		CurrentUser::store($user);
		$engSentenceId = 1;

		$this->Sentence->changeLanguage($engSentenceId, 'jpn');

		$nbTranscr = $this->Sentence->Transcriptions->find()
			->where(['sentence_id' => $engSentenceId])
			->count();
		$this->assertTrue($nbTranscr > 0);
	}

	function testSentenceFlagEditionDeletesTranscriptions() {
		$user = $this->Sentence->Users->get(1);
		CurrentUser::store($user);
		$jpnSentenceId = 6;

		$this->Sentence->changeLanguage($jpnSentenceId, 'deu');

		$nbTranscr = $this->Sentence->Transcriptions->find()
			->where(['sentence_id' => $jpnSentenceId])
			->count();			
		$this->assertTrue($nbTranscr == 0);
	}

	function testSentenceUnadoptionDoesntTouchTranscriptions() {
		$jpnSentenceId = 6;
		$jpnSentenceOwner = 7;
		$conditions = array('sentence_id' => $jpnSentenceId);
		$transcrBefore = $this->Sentence->Transcriptions->find('all')
			->where($conditions)
			->select(['id', 'script', 'text', 'user_id', 'needsReview'])
			->toList();
		$this->Sentence->unsetOwner($jpnSentenceId, $jpnSentenceOwner);

		$transcrAfter = $this->Sentence->Transcriptions->find('all')
			->where($conditions)
			->select(['id', 'script', 'text', 'user_id', 'needsReview'])
			->toList();
		$this->assertEquals($transcrBefore, $transcrAfter);
	}

	function testSentenceDeletionDeletesTranscriptions() {
		$jpnSentence = $this->Sentence->get(6);
		$this->Sentence->delete($jpnSentence);

		$transcr = $this->Sentence->Transcriptions->find('all')
			->where(['sentence_id' => $jpnSentence->id])
			->toList();
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
		$sentence = $this->Sentence->get(1);

		$this->Sentence->delete($sentence);

		try {
			$result = $this->Sentence->get(1);
		} catch (RecordNotFoundException $e) {
			$result = false;
		}
		$this->assertFalse($result);
	}

	function testReturnsTrueOnDelete() {
		$sentence = $this->Sentence->get(1);

		$result = $this->Sentence->delete($sentence);

		$this->assertTrue($result);
	}

	function testReturnsFalseIfAudioOnDelete() {
		$sentence = $this->Sentence->get(3);

		$result = $this->Sentence->delete($sentence);

		$this->assertFalse($result);
	}

	function testTranslationLinksFromSentenceRemovedOnDelete() {
		$sentenceId = 1;
		$sentence = $this->Sentence->get($sentenceId);

		$this->Sentence->delete($sentence);

		$trans = $this->Sentence->Links->findDirectTranslationsIds($sentenceId);
		$this->assertEquals(array(), $trans);
	}

	function testLogsSentenceDeletionOnDelete() {
		$sentence = $this->Sentence->get(1);
		$conditions = array('type' => 'sentence');
		$before = $this->Sentence->Contributions->find()
			->where($conditions)
			->count();

		$this->Sentence->delete($sentence);

		$after = $this->Sentence->Contributions->find()
			->where($conditions)
			->count();
		$added = $after - $before;
		$this->assertEquals(1, $added);
	}

	function testLogsSentenceDeletionWithFieldsOnDelete() {
		$sentenceId = 1;
		$sentence = $this->Sentence->get($sentenceId);
		$expected = array(
			'sentence_id' => $sentenceId,
			'sentence_lang' => $sentence->lang,
			'text' => $sentence->text,
			'action' => 'delete',
		);
		$fields = array('sentence_id', 'sentence_lang', 'text', 'action');
		$conditions = array('type' => 'sentence');
		$before = $this->Sentence->Contributions->deleteAll(['id >' => 0]);

		$this->Sentence->delete($sentence);

		$log = $this->Sentence->Contributions->find('all')
			->where($conditions)
			->select($fields)
			->toList();
		$result = array_intersect_key($log[0]->old_format['Contribution'], $expected);
		$this->assertEquals($expected, $result);
	}

	function testLogsLinkDeletionOnDelete() {
		$sentence = $this->Sentence->get(5);
		$conditions = array('type' => 'link');
		$before = $this->Sentence->Contributions->find('all')
			->where($conditions)
			->count();

		$this->Sentence->delete($sentence);

		$after = $this->Sentence->Contributions->find('all')
			->where($conditions)
			->count();
		$added = $after - $before;
		$this->assertEquals(2, $added);
	}

	function testLogsLinkDeletionWithFieldsOnDelete() {
		$sentenceId = 1;
		$sentence = $this->Sentence->get($sentenceId);
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
		$before = $this->Sentence->Contributions->deleteAll(['id >' => 0]);

		$this->Sentence->delete($sentence);

		$logs = $this->Sentence->Contributions->find('all')
			->where($conditions)
			->select($fields)
			->toList();

		$result = [];
		foreach ($logs as $log) {
			$result[] = ['Contribution' => [
				'sentence_id' => $log->sentence_id,
				'translation_id' => $log->translation_id
			]];
		}

		$this->assertEquals($expected, $result);
	}

	function testTranslationLinksToSentenceRemovedOnDelete() {
		$sentenceId = 1;
		$sentence = $this->Sentence->get($sentenceId);
		$translations = $this->Sentence->Links->findDirectTranslationsIds($sentenceId);

		$this->Sentence->delete($sentence);

		foreach($translations as $transId) {
			$trans = $this->Sentence->Links->findDirectTranslationsIds($transId);
			$this->assertFalse(in_array($sentenceId, $trans));
		}
	}

	function testLanguageCountDecrementedOnDelete() {
		$sentenceId = 1;
		$sentence = $this->Sentence->get($sentenceId);
		$language = $this->Sentence->Languages->findByCode($sentence->lang)->first();
		$countBefore = $language->sentences;

		$this->Sentence->delete($sentence);

		$language = $this->Sentence->Languages->findByCode($sentence->lang)->first();
		$countAfter = $language->sentences;
		$delta = $countAfter - $countBefore;
		$this->assertEquals(-1, $delta);
	}

	function testListsCleanedOnDelete() {
		$sentenceId = 8;
		$sentence = $this->Sentence->get($sentenceId);
		$inListBefore = $this->Sentence->SentencesLists->SentencesSentencesLists
			->findAllBySentenceId($sentenceId)
			->count();

		$this->Sentence->delete($sentence);

		$inListAfter = $this->Sentence->SentencesLists->SentencesSentencesLists
			->findAllBySentenceId($sentenceId)
			->count();
		$delta = $inListAfter - $inListBefore;
		$this->assertEquals(-1, $delta);
	}

	function testTagsAreRemovedOnDelete() {
		$sentenceId = 8;
		$sentence = $this->Sentence->get($sentenceId);
		$tagsBefore = $this->Sentence->TagsSentences->getAllTagsOnSentence($sentenceId);

		$this->Sentence->delete($sentence);

		$tagsAfter = $this->Sentence->TagsSentences->getAllTagsOnSentence($sentenceId);
		$this->assertNotEquals(0, count($tagsBefore));
		$this->assertEquals(0, count($tagsAfter));
	}

	function testScriptIsSetOnSentenceCreation() {
		$cmnSentence = $this->Sentence->newEntity([
			'lang' => 'cmn',
			'text' => '我们试试看！',
		]);

		$savedSentence = $this->Sentence->save($cmnSentence);

		$this->assertEquals('Hans', $savedSentence->script);
	}

	function testScriptIsNotSetOnSentenceCreation() {
		$cmnSentence = $this->Sentence->newEntity([
			'lang' => 'eng',
			'text' => 'Who needs to specify script in English?',
		]);

		$savedSentence = $this->Sentence->save($cmnSentence);

		$this->assertNull($savedSentence->script);
	}

	function testScriptShouldBeValidOnUpdate() {
		$cmnSentence = $this->Sentence->newEntity([
			'id' => 2,
			'script' => 'invalid script code!',
		]);

		$result = $this->Sentence->save($cmnSentence);
		$this->assertFalse($result);
	}

	function testScriptShouldBeValidOnCreate() {
		$cmnSentence = $this->Sentence->newEntity([
			'script' => 'invalid script code!',
			'lang' => 'cmn',
			'text' => '我们试试看！',
		]);

		$result = $this->Sentence->save($cmnSentence);
		$this->assertFalse($result);
	}

	function testScriptShouldBeValidAndCheckType() {
		$cmnSentence = $this->Sentence->newEntity([
			'script' => true,
			'lang' => 'cmn',
			'text' => '我们试试看！',
		]);

		$result = $this->Sentence->save($cmnSentence);
		$this->assertFalse($result);
	}

	function testNeedsReindex() {
		$reindex = array(2, 3);
		$this->Sentence->needsReindex($reindex);
		$result = $this->Sentence->ReindexFlags->find('all')
			->where(['sentence_id' => $reindex], ['sentence_id' => 'integer[]'])
			->count();
		$this->assertEquals(2, $result);
	}

	function testModifiedSentenceNeedsReindex() {
		$id = 1;
		$sentence = $this->Sentence->get($id);
		$sentence->text = 'Changed!';
		$this->Sentence->save($sentence);
		$result = $this->Sentence->ReindexFlags->findBySentenceId($id);
		$this->assertTrue((bool)$result);
	}

	function testModifiedSentenceNeedsTranslationsReindex() {
		$expected = array(1, 2, 4, 5);
		$sentence = $this->Sentence->get(5);
		$sentence->user_id = 0;
		$this->Sentence->save($sentence);
		$result = $this->Sentence->ReindexFlags->find('all')
			->order(['sentence_id'])
			->toList();
		$result = Hash::extract($result, '{n}.sentence_id');
		$this->assertEquals($expected, $result);
	}

	function testRemovedSentenceNeedsItselfAndTranslationsReindex() {
		$expected = array(1, 2, 4, 5);
		$sentence = $this->Sentence->get(5);
		$this->Sentence->delete($sentence);
		$result = $this->Sentence->ReindexFlags->find('all')
			->order(['sentence_id'])
			->toList();
		$result = Hash::extract($result, '{n}.sentence_id');
		$this->assertEquals($expected, $result);
	}

	function testSentenceLoosesOKTagOnEdition() {
		$sentenceId = 2;
		$OKTagId = $this->Sentence->Tags->getIdFromName(
			$this->Sentence->Tags->getOKTagName()
		);
		$this->assertTrue(
			$this->Sentence->TagsSentences->isSentenceTagged($sentenceId, $OKTagId)
		);
		$sentence = $this->Sentence->newEntity([
			'id' => $sentenceId,
			'lang' => 'cmn',
			'text' => "That should remove the OK tag automatically!"
		]);
		$this->Sentence->save($sentence);
		$this->assertFalse(
			$this->Sentence->TagsSentences->isSentenceTagged($sentenceId, $OKTagId)
		);
	}

	function testSentenceDontLoosesOKTagOnFlagChange() {
		$user = $this->Sentence->Users->get(1);
		CurrentUser::store($user);
		$sentenceId = 2;
		$OKTagId = $this->Sentence->Tags->getIdFromName(
			$this->Sentence->Tags->getOKTagName()
		);
		$this->assertTrue(
			$this->Sentence->TagsSentences->isSentenceTagged($sentenceId, $OKTagId)
		);

		$this->Sentence->changeLanguage($sentenceId, 'ita');

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
		$entity = $this->Sentence->get($sentenceId);
		$entity->user_id = null;
		
		$this->Sentence->sphinxAttributesChanged($attributes, $values, $isMVA, $entity);

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
		$entity = $this->Sentence->get($sentenceId);
		$entity->user_id = $ownerId;
		
		$this->Sentence->sphinxAttributesChanged($attributes, $values, $isMVA, $entity);

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
		$entity = $this->Sentence->get($sentenceId);
		$entity->correctness = $correctness;

		$this->Sentence->sphinxAttributesChanged($attributes, $values, $isMVA, $entity);

		$this->assertEquals($expectedAttributes, $attributes);
		$this->assertEquals($expectedValues, $values);
	}

	function testEditSentence_succeedsForSentenceOwner() {
		$user = $this->Sentence->Users->get(4);
		CurrentUser::store($user);
		$this->editSentenceWithSuccess();
	}

	function testEditSentence_succeedsForCorpusMaintainer() {
		$user = $this->Sentence->Users->get(2);
		CurrentUser::store($user);
		$this->editSentenceWithSuccess();
	}

	function editSentenceWithSuccess() {
		$before = $this->Sentence->get(53);
		$data = array(
			'id' => 'eng_53',
			'value' => 'Edited sentence.'
		);
		$sentence = $this->Sentence->editSentence($data);

		$expected = array(
			'id' => 53,
			'lang' => 'eng',
			'text' => 'Edited sentence.'
		);
		$result = array_intersect_key($sentence->toArray(), $expected);
		$this->assertEquals($expected, $result);

		$after = $this->Sentence->get(53);
		$this->assertNotEquals($before->text, $after->text);
		$this->assertNotEquals($before->modified, $after->modified);
	}

	function testEditSentence_succeedsWhenLangEmtpy() {
		$user = $this->Sentence->Users->get(2);
		CurrentUser::store($user);

		$data = array(
			'id' => '_53',
			'value' => 'Sentence with unknown lang.'
		);
		$sentence = $this->Sentence->editSentence($data);

		$expected = array(
			'id' => 53,
			'lang' => null,
			'text' => 'Sentence with unknown lang.'
		);
		$result = array_intersect_key($sentence->toArray(), $expected);

		$this->assertEquals($expected, $result);
	}

	function testEditSentence_failsBecauseHasAudio() {
		$user = $this->Sentence->Users->get(7);
		CurrentUser::store($user);

		$data = array(
			'id' => 'spa_3',
			'value' => 'changing'
		);
		$expected = $this->Sentence->get(3);
		$result = $this->Sentence->editSentence($data);
		
		$this->assertEquals($expected, $result);
	}

	function testEditSentence_failsBecauseNotOwner() {
		$user = $this->Sentence->Users->get(4);
		CurrentUser::store($user);

		$data = array(
			'id' => 'eng_1',
			'value' => 'Edited sentence.'
		);
		$before = $this->Sentence->get(1);

		$result = $this->Sentence->editSentence($data);
		$this->assertNotFalse($result);

		$after = $this->Sentence->get(1);
		$this->assertEquals($before, $after);
	}

	function testEditSentence_failsBecauseWrongId() {
		$user = $this->Sentence->Users->get(4);
		CurrentUser::store($user);
		
		$data = array(
			'id' => '53_eng',
			'value' => 'Edited sentence.'
		);
		$result = $this->Sentence->editSentence($data);
		
		$this->assertEmpty($result);
	}

	function testDeleteSentence_succeedsBecauseIsOwnerAndHasNoTranslations()
	{
		$user = $this->Sentence->Users->get(4);
		CurrentUser::store($user);
		$this->deleteSentenceWithSuccess(53);
	}

	function testDeleteSentence_succeedsBecauseIsCorpusMaintainer()
	{
		$user = $this->Sentence->Users->get(2);
		CurrentUser::store($user);
		$this->deleteSentenceWithSuccess(53);
	}

	function testDeleteSentence_succeedsBecauseIsAdmin()
	{
		$user = $this->Sentence->Users->get(2);
		CurrentUser::store($user);
		$this->deleteSentenceWithSuccess(53);
	}

	function deleteSentenceWithSuccess($id) {
		$result = $this->Sentence->deleteSentence($id);
		$this->assertTrue($result);

		$count = $this->Sentence->find()->where(['id' => $id])->count();
		$this->assertEquals(0, $count);
	}

	function testDeleteSentence_failsIfNotOwnerAndHasNoTranslations()
	{
		$user = $this->Sentence->Users->get(4);
		CurrentUser::store($user);
		$this->deleteSentenceWithFailure(52);
	}

	function testDeleteSentence_failsIfOwnerAndHasTranslations()
	{
		$this->beOwnerOfCurrentSentence(1);
		$this->deleteSentenceWithFailure(1);
	}

	function deleteSentenceWithFailure($id) {
		$result = $this->Sentence->deleteSentence($id);
		$this->assertFalse($result);

		$count = $this->Sentence->find()->where(['id' => $id])->count();
		$this->assertEquals(1, $count);
	}

	function testNumberOfSentencesOwnedBy() {
		$result = $this->Sentence->numberOfSentencesOwnedBy(7);
		$this->assertEquals(21, $result);
	}

	function testGetSentenceTextForId_succeeds() {
		$result = $this->Sentence->getSentenceTextForId(1);
		$expected = 'The fundamental cause of the problem is that in the modern world, idiots are full of confidence, while the intelligent are full of doubt.';
		$this->assertEquals($expected, $result);
	}

	function testGetSentenceTextForId_fails() {
		$result = $this->Sentence->getSentenceTextForId(99999999);
		$this->assertEquals('', $result);
	}

	function testGetLanguageCodeFromSentenceId_succeeds() {
		$result = $this->Sentence->getLanguageCodeFromSentenceId(1);
		$this->assertEquals('eng', $result);
	}

	function testGetLanguageCodeFromSentenceId_fails() {
		$result = $this->Sentence->getLanguageCodeFromSentenceId(99999999);
		$this->assertEquals(null, $result);
	}

	function testChangeLanguage_succeeds() {
		CurrentUser::store($this->Sentence->Users->get(7));
		$result = $this->Sentence->changeLanguage(1, 'jpn');
		$this->assertEquals('jpn', $result);
	}

	function testChangeLanguage_succeedsAsCorpusMaintainter() {
		CurrentUser::store($this->Sentence->Users->get(2));
		$result = $this->Sentence->changeLanguage(1, 'jpn');
		$this->assertEquals('jpn', $result);
	}

	function testChangeLanguage_failsBecauseNowAllowed() {
		CurrentUser::store($this->Sentence->Users->get(4));
		$result = $this->Sentence->changeLanguage(1, 'jpn');
		$this->assertEquals('eng', $result);
	}

	function testChangeLanguage_failsBecauseWrongSentenceId() {
		$result = $this->Sentence->changeLanguage(9999999, 'jpn');
		$this->assertFalse($result);
	}

	function testChangeLanguage_failsBecauseAudio() {
		CurrentUser::store($this->Sentence->Users->get(2));
		$result = $this->Sentence->changeLanguage(3, 'eng');
		$this->assertEquals('spa', $result);
	}

	function testSetOwner_succeeds() {
		$id = 14;
		$before = $this->Sentence->get($id)->user_id;

		$result = $this->Sentence->setOwner($id, 7, User::ROLE_CONTRIBUTOR);
		$this->assertTrue($result);

		$after = $this->Sentence->get($id)->user_id;
		$this->assertNotEquals($before, $after);
	}

	function testSetOwner_failsIfNotOrphan() {
		$id = 1;
		$before = $this->Sentence->get($id)->user_id;

		$result = $this->Sentence->setOwner($id, 1, User::ROLE_ADMIN);
		$this->assertFalse($result);

		$after = $this->Sentence->get($id)->user_id;
		$this->assertEquals($before, $after);
	}

	function testUnsetOwner_succeeds() {
		$id = 1;
		$before = $this->Sentence->get($id)->user_id;

		$result = $this->Sentence->unsetOwner($id, 7);
		$this->assertTrue($result);

		$after = $this->Sentence->get($id)->user_id;
		$this->assertNotEquals($before, $after);
	}

	function testUnsetOwner_failsIfNotOwner() {
		$id = 1;
		$before = $this->Sentence->get($id)->user_id;

		$result = $this->Sentence->unsetOwner($id, 1);
		$this->assertFalse($result);

		$after = $this->Sentence->get($id)->user_id;
		$this->assertEquals($before, $after);
	}

	function testSave_doesntAddDuplicate() {
		$sentence = $this->Sentence->saveNewSentence(
			'This is a lonely sentence.', 'eng', 1
		);
		$this->assertEquals(7, $sentence->id);
		$this->assertTrue($sentence->isDuplicate);
	}

	function testGetTranslationsOf() {
		$results = $this->Sentence->getTranslationsOf(1);
		$directTranslationsIds = Hash::extract($results[0], '{n}.id');
		$indirectTranslationsIds = Hash::extract($results[1], '{n}.id');
		$this->assertEquals([2, 4, 3], $directTranslationsIds);
		$this->assertEquals([5, 6], $indirectTranslationsIds);
	}

	function testEditCorrectness() {
		$result = $this->Sentence->editCorrectness(1, -1);
		$this->assertEquals(-1, $result->correctness);
	}

	function testGetNeighborsSentenceIds() {
		$result = $this->Sentence->getNeighborsSentenceIds(8, 'fra');
		$expected = [
			'prev' => 4,
			'next' => 12
		];
		$this->assertEquals($expected, $result);
	}

	function testFindFilteredTranslations_withLangSettings() {
		$Users = TableRegistry::getTableLocator()->get('Users');
		$user = $Users->get(4)->toArray();
		CurrentUser::store($user);

		$result = $this->Sentence->find('filteredTranslations')
			->where(['Sentences.id' => 1])
			->contain($this->Sentence->contain())
			->first();
		
		$expected = ['fra', 'deu'];
		$directTranslationsLangs = Hash::extract($result->translations[0], '{n}.lang');
		$indirectTranslationsLangs = Hash::extract($result->translations[1], '{n}.lang');
		
		$languages = array_unique(array_merge($directTranslationsLangs, $indirectTranslationsLangs));
		$this->assertEquals(asort($expected), asort($languages));
	}

	function testFindFilteredTranslations_withLangSettingsAndTranslationLang() {
		$Users = TableRegistry::getTableLocator()->get('Users');
		$user = $Users->get(4)->toArray();
		CurrentUser::store($user);

		$result = $this->Sentence->find('filteredTranslations', ['translationLang' => 'jpn'])
			->where(['Sentences.id' => 1])
			->contain($this->Sentence->contain())
			->first();
		
		$expected = ['jpn'];
		$directTranslationsLangs = Hash::extract($result->translations[0], '{n}.lang');
		$indirectTranslationsLangs = Hash::extract($result->translations[1], '{n}.lang');
		
		$languages = array_unique(array_merge($directTranslationsLangs, $indirectTranslationsLangs));
		$this->assertEquals(asort($expected), asort($languages));
	}

	function testFindFilteredTranslations_withoutLangSettings() {
		$result = $this->Sentence->find('filteredTranslations')
			->where(['Sentences.id' => 1])
			->contain($this->Sentence->contain())
			->first();
		
		$expected = ['fra', 'spa', 'deu', 'cmn', 'jpn'];
		$directTranslationsLangs = Hash::extract($result->translations[0], '{n}.lang');
		$indirectTranslationsLangs = Hash::extract($result->translations[1], '{n}.lang');

		$languages = array_unique(array_merge($directTranslationsLangs, $indirectTranslationsLangs));
		$this->assertEquals(asort($expected), asort($languages));
	}

	function testGetSentenceWithId_translationsHaveAudioInfo() {
		CurrentUser::store(null);
		$sentence = $this->Sentence->getSentenceWithId(1);
		$result = [];
		foreach($sentence->translations as $translationsGroup) {
			foreach($translationsGroup as $translation) {
				$audios = $translation->audios;
				$result[$translation->id] = isset($audios[0]) ? $audios[0]->user_id : null;
			}
		}
		$expected = [
			2 => null,
			3 => 4,
			4 => null,
			5 => null,
			6 => null,
		];
		$this->assertEquals($expected, $result);
	}

    function testSaveNewSentence_correctDateUsingArabicLocale() {
        I18n::setLocale('ar');
        $added = $this->Sentence->saveNewSentence('test', 'eng', 1);
        $returned = $this->Sentence->get($added->id);
        $this->assertEquals($added->created, $returned->created);
        $this->assertEquals($added->modified, $returned->modified);
    }
}
