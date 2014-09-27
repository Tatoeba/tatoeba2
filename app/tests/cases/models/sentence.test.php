<?php
/* Sentence Test cases generated on: 2014-04-15 01:07:30 : 1397516850*/
App::import('Model', 'Sentence');

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
	);

	function startTest() {
		$this->Sentence =& ClassRegistry::init('Sentence');
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

	function _assertJapaneseReading($type, $japanese, $reading) {
		$result = $this->Sentence->getJapaneseRomanization2($japanese, Sentence::$romanji[$type]);
		$this->assertEqual($reading, $result, "$type of '$japanese' should read '$reading', not '$result'");
	}

	function testGetJapaneseRomanization2_furigana() {
		$this->_assertJapaneseReading('furigana', '例えば', 'たとえば');
	}

	function testGetJapaneseRomanization2_mix() {
		$this->_assertJapaneseReading('mix', '例えば', '例えば[たとえば]');
	}

	function testGetJapaneseRomanization2_romaji() {
		$this->_assertJapaneseReading('romanji', '例えば', 'tatoeba');
		$this->_assertJapaneseReading('romanji', 'やった', 'ya tta');
		$this->_assertJapaneseReading('romanji', 'それはとってもいい話だ', 'sore ha tottemo ii hanashi da');
	}
}
