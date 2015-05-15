<?php
/* Sentence Test cases generated on: 2014-04-15 01:07:30 : 1397516850*/
App::import('Model', 'Sentence');
App::import('Behavior', 'Sphinx');

class SentenceTestCase extends CakeTestCase {
	var $fixtures = array(
		'app.sentence',
		'app.user',
		'app.group',
		'app.country',
		'app.sentence_comment',
		'app.contribution',
		'app.sentences_list',
		'app.sentences_sentences_list',
		'app.wall',
		'app.wall_thread',
		'app.favorites_user',
		'app.tag',
		'app.tags_sentence',
		'app.language',
		'app.link',
		'app.sentence_annotation',
		'app.transcription',
	);

	function startTest() {
		$this->Sentence =& ClassRegistry::init('Sentence');
		Mock::generate('SphinxBehavior');
		$this->Sentence->Behaviors->Sphinx =& new MockSphinxBehavior();
	}

	function endTest() {
		unset($this->Sentence);
		ClassRegistry::flush();
	}

	function testSaveNewSentence_addsOneSentence() {
		$oldNumberOfSentences = $this->Sentence->find('count');
		$this->Sentence->saveNewSentence('Hello world.', 'eng', 1);
		$newNumberOfSentences = $this->Sentence->find('count');
		$sentencesAdded = $newNumberOfSentences - $oldNumberOfSentences;

		$this->assertEqual($sentencesAdded, 1);
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
		$this->assertTrue($returnValue);
	}

	function testSaveTranslation_links() {
		Mock::generate('Link');
		$translationFromSentenceId = 1;
		$newlyCreatedSentenceId = (string)($this->Sentence->find('count') + 1);
		$this->Sentence->Link =& new MockLink();

		$this->Sentence->Link->expectOnce(
			'add',
			array($translationFromSentenceId, $newlyCreatedSentenceId, 'eng', 'eng')
		);

		$this->Sentence->saveTranslation(
			$translationFromSentenceId,
			'eng',
			'This is the translation.',
			'eng',
			Sentence::MAX_CORRECTNESS
		);
	}

    function testSentenceAdditionAddsTranscriptions() {
        $result = $this->Sentence->save(array(
            'text' => '歌舞伎ってご存知ですか？',
            'lang' => 'jpn'
        ));
        $newSentence = $this->Sentence->getLastInsertID();
        $transcriptions = $this->Sentence->Transcription->find(
            'count',
            array('conditions' => array('sentence_id' => $newSentence))
        );
        $this->assertEqual(2, $transcriptions);
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
		$this->assertEqual(count($transcrBefore), count($transcrAfter));
		$this->assertNotEqual($transcrBefore, $transcrAfter);
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
		$this->assertEqual($transcrBefore, $transcrAfter);
	}

	function testSentenceDeletionDeletesTranscriptions() {
		$jpnSentenceId = 6;
		$this->Sentence->delete($jpnSentenceId, false);

		$transcr = $this->Sentence->Transcription->find('all', array(
			'conditions' => array('sentence_id' => $jpnSentenceId),
		));
		$this->assertEqual(array(), $transcr);
	}

	function testGetSentencesLang_returnsLang() {
		$result = $this->Sentence->getSentencesLang(array(3, 4, 8));
		$expectedLangs = array(3 => 'spa', 4 => 'fra', 8 => 'fra');
		$this->assertEqual($expectedLangs, $result);
	}

	function testGetSentencesLang_returnsLangId() {
		$spaId = 3;
		$fraId = 4;
		$result = $this->Sentence->getSentencesLang(array(3, 4, 8), true);
		$expectedLangs = array(3 => $spaId, 4 => $fraId, 8 => $fraId);
		$this->assertEqual($expectedLangs, $result);
	}

	function testGetSentencesLang_returnsNullForFlaglessSentences() {
		$result = $this->Sentence->getSentencesLang(array(9));
		$expectedLangs = array(9 => null);
		$this->assertEqual($expectedLangs, $result);
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
		$sentenceId = 1;
		$this->Sentence->id = $sentenceId;
		$this->Sentence->saveField('hasaudio', 'from_users');

		$result = $this->Sentence->delete($sentenceId, false);

		$this->assertFalse($result);
	}

	function testTranslationLinksFromSentenceRemovedOnDelete() {
		$sentenceId = 1;

		$this->Sentence->delete($sentenceId, false);

		$trans = $this->Sentence->Link->findDirectTranslationsIds($sentenceId);
		$this->assertEqual(array(), $trans);
	}

	function testLogsSentenceDeletionOnDelete() {
		$sentenceId = 1;
		$conditions = array('type' => 'sentence');
		$before = $this->Sentence->Contribution->find('count', compact('conditions'));

		$this->Sentence->delete($sentenceId, false);

		$after = $this->Sentence->Contribution->find('count', compact('conditions'));
		$added = $after - $before;
		$this->assertEqual(1, $added);
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
		$this->assertEqual($expected, $log[0]['Contribution']);
	}

	function testLogsLinkDeletionOnDelete() {
		$sentenceId = 3;
		$conditions = array('type' => 'link');
		$before = $this->Sentence->Contribution->find('count', compact('conditions'));

		$this->Sentence->delete($sentenceId, false);

		$after = $this->Sentence->Contribution->find('count', compact('conditions'));
		$added = $after - $before;
		$this->assertEqual(2, $added);
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
		$this->assertEqual($expected, $logs);
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
		$this->assertEqual(-1, $delta);
	}

	function testListsCleanedOnDelete() {
		$sentenceId = 4;
		$inListBefore = $this->Sentence->SentencesList->SentencesSentencesLists->findAllBySentenceId($sentenceId);

		$this->Sentence->delete($sentenceId, false);

		$inListAfter = $this->Sentence->SentencesList->SentencesSentencesLists->findAllBySentenceId($sentenceId);
		$delta = count($inListAfter) - count($inListBefore);
		$this->assertEqual(-1, $delta);
	}

	function testTagsAreRemovedOnDelete() {
		$sentenceId = 8;
		$tagsBefore = $this->Sentence->TagsSentences->getAllTagsOnSentence($sentenceId);

		$this->Sentence->delete($sentenceId, false);

		$tagsAfter = $this->Sentence->TagsSentences->getAllTagsOnSentence($sentenceId);
		$delta = count($tagsAfter) - count($tagsBefore);
		$this->assertEqual(-1, $delta);
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

		$this->assertFalse($isMVA);
		$this->assertEqual($expectedAttributes, $attributes);
		$this->assertEqual($expectedValues, $values);
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

		$this->assertFalse($isMVA);
		$this->assertEqual($expectedAttributes, $attributes);
		$this->assertEqual($expectedValues, $values);
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

		$this->assertFalse($isMVA);
		$this->assertEqual($expectedAttributes, $attributes);
		$this->assertEqual($expectedValues, $values);
	}
}
