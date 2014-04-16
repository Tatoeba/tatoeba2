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
}
