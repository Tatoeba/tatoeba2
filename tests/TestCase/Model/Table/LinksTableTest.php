<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LinksTable;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class LinksTableTest extends TestCase {
	public $fixtures = array(
		'app.Sentences',
		'app.Users',
		'app.SentenceComments',
		'app.Contributions',
		'app.SentencesLists',
		'app.SentencesSentencesLists',
		'app.Walls',
		'app.WallThreads',
		'app.FavoritesUsers',
		'app.Tags',
		'app.TagsSentences',
		'app.Languages',
		'app.Links',
		'app.SentenceAnnotations',
		'app.Transcriptions',
		'app.ReindexFlags'
	);

	function setUp() {
		parent::setUp();
		$this->Link = TableRegistry::getTableLocator()->get('Links');
	}

	function tearDown() {
		unset($this->Link);
		parent::tearDown();
	}

	function testAdd_linksSentenceToTranslation() {
		$sentenceId = 5;
		$translationId = 7;
		$this->Link->add($sentenceId, $translationId);

		$newLink = $this->Link->find()
			->where([
				'sentence_id' => $sentenceId,
				'translation_id' => $translationId,
			])
			->first();
		$this->assertTrue((bool)$newLink);
	}

	function testAdd_linksTranslationToSentence() {
		$sentenceId = 5;
		$translationId = 7;
		$this->Link->add($sentenceId, $translationId);

		$newLink = $this->Link->find()
			->where([
				'translation_id' => $sentenceId,
				'sentence_id' => $translationId,
			])
			->first();
		$this->assertTrue((bool)$newLink);
	}

	function testDeletePairRemovesBothWays() {
		$sentenceId = 4;
		$translationId = 6;
		$this->Link->deletePair($sentenceId, $translationId);
		$result = $this->Link->find()
			->where(['OR' => [
				[
					'sentence_id'    => $translationId,
					'translation_id' => $sentenceId,
				],
				[
					'sentence_id'    => $sentenceId,
					'translation_id' => $translationId,
				]
			]])
			->count();
		$this->assertEquals(0, $result);
	}

	function testDeletePairLogsDeletion() {
		$sentenceId = 4;
		$translationId = 6;
		$this->Link->deletePair($sentenceId, $translationId);

		$nbLogs = TableRegistry::getTableLocator()->get('Contributions')
			->find('all')
			->where(['OR' => [
				[
					'sentence_id'    => $translationId,
					'translation_id' => $sentenceId,
					'action'         => 'delete',
				],
				[
					'sentence_id'    => $sentenceId,
					'translation_id' => $translationId,
					'action'         => 'delete',
				]
			]])
			->count();
		$this->assertEquals($nbLogs, 2);
	}

	function testFindDirectAndIndirectTranslationsIds_worksWithLonelySentences() {
		$lonelySentenceId = 7;
		$expectedLinkedSentences = array();

		$result = $this->Link->findDirectAndIndirectTranslationsIds($lonelySentenceId);

		$this->assertEquals($result, $expectedLinkedSentences);
	}

	function testFindDirectAndIndirectTranslationsIds_doesNotReturnDuplicates() {
		$sentenceId = 2;

		$result = $this->Link->findDirectAndIndirectTranslationsIds($sentenceId);
		$filteredResult = array_unique($result);

		$this->assertEquals($result, $filteredResult);
	}

	function testFindDirectAndIndirectTranslationsIds_walksWholeGraph() {
		$sentenceId = 2;
		$expectedLinkedSentences = array(1, 3, 4, 5, 6);

		$result = $this->Link->findDirectAndIndirectTranslationsIds($sentenceId);
		sort($result);

		$this->assertEquals($result, $expectedLinkedSentences);
	}

	function testFindDirectAndIndirectTranslationsIds_walksPartsOfGraph() {
		$sentenceId = 5;
		$expectedLinkedSentences = array(1, 2, 4);

		$result = $this->Link->findDirectAndIndirectTranslationsIds($sentenceId);
		sort($result);

		$this->assertEquals($result, $expectedLinkedSentences);
	}

	function testFindDirectTranslationsIds_worksWithLonelySentences() {
		$lonelySentenceId = 7;
		$expectedLinkedSentences = array();

		$result = $this->Link->findDirectTranslationsIds($lonelySentenceId);

		$this->assertEquals($result, $expectedLinkedSentences);
	}

	function testFindDirectTranslationsIds_doesNotReturnDuplicates() {
		$sentenceId = 2;

		$result = $this->Link->findDirectTranslationsIds($sentenceId);
		$filteredResult = array_unique($result);

		$this->assertEquals($result, $filteredResult);
	}

	function testFindDirectTranslationsIds_walksGraph() {
		$sentenceId = 2;
		$expectedLinkedSentences = array(1, 4, 5);

		$result = $this->Link->findDirectTranslationsIds($sentenceId);
		sort($result);

		$this->assertEquals($result, $expectedLinkedSentences);
	}

	function testAdd_flagSentencesToReindex() {
		/* Linking 8 and 5 is done in two steps:
                 *   8->5 => 8 and its direct translations (8) can now see 5
		 *   5->8 => 5 and its direct translations (2,5) can now see 8
                 * Result: $expected = 8, 2, 5
                 */
		$expected = array(8, 2, 5);
		$this->Link->add(8, 5);
		$result = $this->Link->Sentences->ReindexFlags->find('all')->toList();
		$result = Hash::extract($result, '{n}.sentence_id');
		$this->assertEquals($expected, $result);
	}

	function testDelete_flagSentencesToReindex() {
		/* Unlinking 1 and 2 is done in two steps:
                     delete 1->2 => 1 and its direct translations (1,3,4) stop seeing 2
                     delete 2->1 => 2 and its direct translations (2,4,5) stop seeing 2
                 * Result: $expected = 1, 3, 4, 2, 4, 5
                 */
		$expected = array(1, 3, 4, 2, 4, 5);
		$this->Link->deletePair(1, 2);
		$result = $this->Link->Sentences->ReindexFlags->find('all')->toList();
		$result = Hash::extract($result, '{n}.sentence_id');
		$this->assertEquals($expected, $result);
	}
}
