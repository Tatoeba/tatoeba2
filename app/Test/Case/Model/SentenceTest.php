<?php
/* Sentence Test cases generated on: 2014-04-15 01:07:30 : 1397516850*/
App::import('Model', 'Sentence');
App::import('Behavior', 'Sphinx');

class SentenceTest extends CakeTestCase {
	public $fixtures = array(
		'app.sentence',
		'app.user',
		'app.users_language',
		'app.contribution',
		'app.sentences_list',
		'app.sentences_sentences_list',
		'app.tag',
		'app.tags_sentence',
		'app.language',
		'app.link',
		'app.transcription',
		'app.reindex_flag',
		'app.audio',
	);

	function startTest($method) {
		$this->Sentence = ClassRegistry::init('Sentence');

		$this->Sentence->Behaviors->Sphinx = Mockery::mock();

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

	function endTest($method) {
		unset($this->Sentence);
		ClassRegistry::flush();
	}

	function testSaveNewSentence_addsOneSentence() {
		$oldNumberOfSentences = $this->Sentence->find('count');
		$this->Sentence->saveNewSentence('Hello world.', 'eng', 1);
		$newNumberOfSentences = $this->Sentence->find('count');
		$sentencesAdded = $newNumberOfSentences - $oldNumberOfSentences;

		$this->assertEquals($sentencesAdded, 1);
	}

	function testSaveNewSentence_nullifiesEmptyLangs() {
		$text = 'Hello world.';

		$this->Sentence->saveNewSentence($text, '', 1);
		$savedSentence = $this->Sentence->find(
			'first',
			array('conditions' => array('Sentence.text' => $text))
		);

		$this->assertNull($savedSentence['Sentence']['lang']);
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
		$data = array(
			'text' => 'Hi there!',
		);
		$result = $this->Sentence->save($data);
		$this->assertTrue((bool)$result);
	}

	function testSave_checksValidLicense() {
		$data = array(
			'text' => 'Trying to save a sentence with an invalid license.',
			'license' => 'some-strange-thing',
		);
		$result = $this->Sentence->save($data);
		$this->assertFalse((bool)$result);
	}

	function testSave_setsDefaultLicenseSettingOnCreation() {
		$data = array(
			'text' => 'This sentence should get a default licence.',
			'user_id' => 7,
		);

		$this->Sentence->create();
		$this->Sentence->save($data);

		$savedSentence = $this->Sentence->findById($this->Sentence->id);
		$this->assertEquals('CC0 1.0', $savedSentence['Sentence']['license']);
	}

	function testSave_doesNotChangeLicenseOnUpdate() {
		$data = array(
			'id' => 1,
			'text' => 'Updating sentence #1.',
			'user_id' => 7,
		);

		$this->Sentence->save($data);

		$savedSentence = $this->Sentence->findById($this->Sentence->id);
		$this->assertEquals('CC BY 2.0 FR', $savedSentence['Sentence']['license']);
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

	function testReturnsFalseIfAudioOnEdit() {
		$result = $this->Sentence->editSentence(3, 'spa', 'changing');

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
}
