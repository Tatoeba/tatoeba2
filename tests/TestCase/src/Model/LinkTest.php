<?php
/* Link Test cases generated on: 2014-04-16 04:19:37 : 1397614777*/
namespace App\Test\TestCase\Model;

use App\Model\Link;
use Cake\TestSuite\TestCase;

class LinkTest extends TestCase {
	public $fixtures = array(
		'app.sentences',
		'app.users',
		'app.groups',
		'app.sentence_comments',
		'app.contributions',
		'app.sentences_lists',
		'app.sentences_sentences_lists',
		'app.walls',
		'app.wall_threads',
		'app.favorites_users',
		'app.tags',
		'app.tags_sentences',
		'app.languages',
		'app.links',
		'app.sentence_annotations',
		'app.transcriptions',
		'app.reindex_flags'
	);

	function startTest($method) {
		$this->Link = ClassRegistry::init('Link');
	}

	function endTest($method) {
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
		$this->assertTrue((bool)$newLink);
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
		$this->assertTrue((bool)$newLink);
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

	function testAdd_flagSentencesToReindex() {
		/* Linking 8 and 5 is done in two steps:
                 *   8->5 => 8 and its direct translations (8) can now see 5
		 *   5->8 => 5 and its direct translations (2,5) can now see 8
                 * Result: $expected = 8, 2, 5
                 */
		$expected = array(8, 2, 5);
		$this->Link->add(8, 5);
		$result = $this->Link->Sentence->ReindexFlag->find('all');
		$result = Set::classicExtract($result, '{n}.ReindexFlag.sentence_id');
		$this->assertEqual($expected, $result);
	}

	function testDelete_flagSentencesToReindex() {
		/* Unlinking 1 and 2 is done in two steps:
                     delete 1->2 => 1 and its direct translations (1,3,4) stop seeing 2
                     delete 2->1 => 2 and its direct translations (2,4,5) stop seeing 2
                 * Result: $expected = 1, 3, 4, 2, 4, 5
                 */
		$expected = array(1, 3, 4, 2, 4, 5);
		$this->Link->deletePair(1, 2);
		$result = $this->Link->Sentence->ReindexFlag->find('all');
		$result = Set::classicExtract($result, '{n}.ReindexFlag.sentence_id');
		$this->assertEqual($expected, $result);
	}
}
