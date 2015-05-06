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
		'app.transcription',
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

	function testDeletePairRemovesBothWays() {
		$sentenceId = 4;
		$translationId = 6;
		$this->Link->deletePair($sentenceId, $translationId);
		$result = $this->Link->find('count', array('conditions' => array(
			'or' => array(
				array('and' => array(
					'Link.sentence_id'    => $translationId,
					'Link.translation_id' => $sentenceId,
				)),
				array('and' => array(
					'Link.sentence_id'    => $sentenceId,
					'Link.translation_id' => $translationId,
				)),
			)
		)));
		$this->assertEqual($result, 0);
	}

	function testDeletePairLogsDeletion() {
		$sentenceId = 4;
		$translationId = 6;
		$this->Link->deletePair($sentenceId, $translationId);

		$nbLogs = ClassRegistry::init('Contribution')
			->find('count', array('conditions' => array(
				'or' => array(
					array('and' => array(
						'Contribution.sentence_id'    => $translationId,
						'Contribution.translation_id' => $sentenceId,
						'Contribution.action'         => 'delete',
					)),
					array('and' => array(
						'Contribution.sentence_id'    => $sentenceId,
						'Contribution.translation_id' => $translationId,
						'Contribution.action'         => 'delete',
					)),
				)
			)));
		$this->assertEqual($nbLogs, 2);
	}

	function testFindDirectAndIndirectTranslationsIds_worksWithLonelySentences() {
		$lonelySentenceId = 7;
		$expectedLinkedSentences = array();

		$result = $this->Link->findDirectAndIndirectTranslationsIds($lonelySentenceId);

		$this->assertEqual($result, $expectedLinkedSentences);
	}

	function testFindDirectAndIndirectTranslationsIds_doesNotReturnDuplicates() {
		$sentenceId = 2;

		$result = $this->Link->findDirectAndIndirectTranslationsIds($sentenceId);
		$filteredResult = array_unique($result);

		$this->assertEqual($result, $filteredResult);
	}

	function testFindDirectAndIndirectTranslationsIds_walksWholeGraph() {
		$sentenceId = 2;
		$expectedLinkedSentences = array(1, 3, 4, 5, 6);

		$result = $this->Link->findDirectAndIndirectTranslationsIds($sentenceId);
		sort($result);

		$this->assertEqual($result, $expectedLinkedSentences);
	}

	function testFindDirectAndIndirectTranslationsIds_walksPartsOfGraph() {
		$sentenceId = 5;
		$expectedLinkedSentences = array(1, 2, 4);

		$result = $this->Link->findDirectAndIndirectTranslationsIds($sentenceId);
		sort($result);

		$this->assertEqual($result, $expectedLinkedSentences);
	}

	function testFindDirectTranslationsIds_worksWithLonelySentences() {
		$lonelySentenceId = 7;
		$expectedLinkedSentences = array();

		$result = $this->Link->findDirectTranslationsIds($lonelySentenceId);

		$this->assertEqual($result, $expectedLinkedSentences);
	}

	function testFindDirectTranslationsIds_doesNotReturnDuplicates() {
		$sentenceId = 2;

		$result = $this->Link->findDirectTranslationsIds($sentenceId);
		$filteredResult = array_unique($result);

		$this->assertEqual($result, $filteredResult);
	}

	function testFindDirectTranslationsIds_walksGraph() {
		$sentenceId = 2;
		$expectedLinkedSentences = array(1, 4, 5);

		$result = $this->Link->findDirectTranslationsIds($sentenceId);
		sort($result);

		$this->assertEqual($result, $expectedLinkedSentences);
	}

	function testAdd_updatesSphinxAttributes() {
		$engId = 1; $cmnId = 2; $spaId = 3;
		$fraId = 4; $deuId = 5; $jpnId = 6;
		$expectedAttributes = array('trans_id');
		$expectedValues = array(
			/* #8 is empty since we simulate the link creation */
			8 => array(array()),
			2 => array(array($engId, $spaId, $fraId, $deuId, $jpnId)),
			5 => array(array($engId, $cmnId, $fraId)),
		);

		$this->Link->data['Link'] = array(
			'sentence_id' => 8,
			'translation_id' => 5
		);
		$this->Link->sphinxAttributesChanged($attributes, $values, $isMVA);

		$this->assertTrue($isMVA);
		$this->assertEqual($expectedAttributes, $attributes);
		$this->assertEqual($expectedValues, $values);
	}
}
