<?php
/* Link Test cases generated on: 2014-04-16 04:19:37 : 1397614777*/
App::import('Model', 'Link');

class LinkTestCase extends CakeTestCase {
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
		$this->Link =& ClassRegistry::init('Link');
	}

	function endTest() {
		unset($this->Link);
		ClassRegistry::flush();
	}

	function testAdd_linksSentenceToTranslation() {
		$sentenceId = 5;
		$translationId = 7;
		$this->Link->add($sentenceId, $translationId);

		$newLink = $this->Link->find('first',
			array('conditions' => array(
				'Link.sentence_id' => $sentenceId,
				'Link.translation_id' => $translationId,
			))
		);
		$this->assertTrue($newLink);
	}

	function testAdd_linksTranslationToSentence() {
		$sentenceId = 5;
		$translationId = 7;
		$this->Link->add($sentenceId, $translationId);

		$newLink = $this->Link->find('first',
			array('conditions' => array(
				'Link.translation_id' => $sentenceId,
				'Link.sentence_id' => $translationId,
			))
		);
		$this->assertTrue($newLink);
	}
}
