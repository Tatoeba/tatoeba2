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

		$this->Sentence->save(array(
			'id' => $jpnSentenceId,
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

		$this->Sentence->save(array(
			'id' => $engSentenceId,
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

		$this->Sentence->save(array(
			'id' => $jpnSentenceId,
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
