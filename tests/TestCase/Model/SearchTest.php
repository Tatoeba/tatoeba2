<?php
namespace App\Test\TestCase\Model;

include_once(APP.'Lib/SphinxClient.php'); // needed to get the constants
use App\Model\Search;
use Cake\TestSuite\TestCase;

class SearchTest extends TestCase
{
    public $fixtures = [
        'app.sentences_lists',
        'app.users_languages',
        'app.tags',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->Search = new Search();
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->Search);
    }

    public function testWithoutFilters() {
        $expected = ['index' => ['und_index'], 'matchMode' => SPH_MATCH_EXTENDED2];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function assertQuery($expectedQuery, $query) {
        $result = $this->Search->filterByQuery($query);
        $this->assertEquals($expectedQuery, $result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'query' => $expectedQuery
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByQuery() {
        $this->assertQuery('comme ci comme ça', 'comme ci comme ça');
    }

    public function testfilterByQuery_empty() {
        $this->assertQuery('', '');
    }

    public function testfilterByLanguage_validLang() {
        $result = $this->Search->filterByLanguage('por');
        $this->assertEquals('por', $result);

        $expected = [
            'index' => ['por_main_index', 'por_delta_index'],
            'matchMode' => SPH_MATCH_EXTENDED2
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByLanguage_und() {
        $result = $this->Search->filterByLanguage('und');
        $this->assertNull($result);

        $expected = ['index' => ['und_index'], 'matchMode' => SPH_MATCH_EXTENDED2];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByLanguage_invalidLang() {
        $result = $this->Search->filterByLanguage('1234567890');
        $this->assertNull($result);

        $expected = ['index' => ['und_index'], 'matchMode' => SPH_MATCH_EXTENDED2];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByLanguage_resets() {
        $this->testfilterByLanguage_validLang();
        $this->testfilterByLanguage_und();
    }

    public function testfilterByOwnerId() {
        $result = $this->Search->filterByOwnerId(4);
        $this->assertEquals(4, $result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'filter' => [['user_id', 4]]
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOwnerId_null() {
        $result = $this->Search->filterByOwnerId(null);
        $this->assertNull($result);

        $expected = ['index' => ['und_index'], 'matchMode' => SPH_MATCH_EXTENDED2];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOrphanship_true() {
        $result = $this->Search->filterByOrphanship(true);
        $this->assertTrue($result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'filter' => [['user_id', 0, false]]
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOrphanship_false() {
        $result = $this->Search->filterByOrphanship(false);
        $this->assertFalse($result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'filter' => [['user_id', 0, true]]
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOrphanship_null() {
        $result = $this->Search->filterByOrphanship(null);
        $this->assertNull($result);

        $expected = ['index' => ['und_index'], 'matchMode' => SPH_MATCH_EXTENDED2];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByCorrectness_true() {
        $result = $this->Search->filterByCorrectness(true);
        $this->assertTrue($result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'filter' => [['ucorrectness', 127, false]]
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByCorrectness_false() {
        $result = $this->Search->filterByCorrectness(false);
        $this->assertFalse($result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'filter' => [['ucorrectness', 127, true]]
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByCorrectness_null() {
        $result = $this->Search->filterByCorrectness(null);
        $this->assertNull($result);

        $expected = ['index' => ['und_index'], 'matchMode' => SPH_MATCH_EXTENDED2];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByAudio_true() {
        $result = $this->Search->filterByAudio(true);
        $this->assertTrue($result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'filter' => [['has_audio', 1]]
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByAudio_false() {
        $result = $this->Search->filterByAudio(false);
        $this->assertFalse($result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'filter' => [['has_audio', 0]]
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByAudio_null() {
        $result = $this->Search->filterByAudio(null);
        $this->assertNull($result);

        $expected = ['index' => ['und_index'], 'matchMode' => SPH_MATCH_EXTENDED2];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_invalid() {
        $currentUserId = null;
        $listId = 999999999999;
        $result = $this->Search->filterByListId($listId, $currentUserId);
        $this->assertFalse($result);

        $expected = ['index' => ['und_index'], 'matchMode' => SPH_MATCH_EXTENDED2];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_public() {
        $currentUserId = null;
        $listId = 2;
        $result = $this->Search->filterByListId($listId, $currentUserId);
        $this->assertTrue($result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'filter' => [['lists_id', $listId]]
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_unlisted() {
        $currentUserId = null;
        $listId = 1;
        $result = $this->Search->filterByListId($listId, $currentUserId);
        $this->assertTrue($result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'filter' => [['lists_id', $listId]]
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_private_isNotOwner() {
        $currentUserId = 1;
        $listId = 3;
        $result = $this->Search->filterByListId($listId, $currentUserId);
        $this->assertFalse($result);

        $expected = ['index' => ['und_index'], 'matchMode' => SPH_MATCH_EXTENDED2];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_private_isOwner() {
        $currentUserId = 7;
        $listId = 3;
        $result = $this->Search->filterByListId($listId, $currentUserId);
        $this->assertTrue($result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'filter' => [['lists_id', $listId]]
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_empty() {
        $currentUserId = null;
        $listId = '';
        $result = $this->Search->filterByListId($listId, $currentUserId);
        $this->assertTrue($result);

        $expected = ['index' => ['und_index'], 'matchMode' => SPH_MATCH_EXTENDED2];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_resets() {
        $this->testfilterByListId_public();
        $this->testfilterByListId_empty();
    }

    public function testfilterByNativeSpeaker_fra() {
        $this->Search->filterByLanguage('fra');
        $result = $this->Search->filterByNativeSpeaker(true);
        $this->assertTrue($result);

        $expected = [
            'index' => ['fra_main_index', 'fra_delta_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'filter' => [['user_id', [4, 3]]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByNativeSpeaker_null() {
        $this->Search->filterByLanguage('fra');
        $result = $this->Search->filterByNativeSpeaker(null);
        $this->assertNull($result);

        $expected = [
            'index' => ['fra_main_index', 'fra_delta_index'],
            'matchMode' => SPH_MATCH_EXTENDED2
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByNativeSpeaker_resets() {
        $this->testfilterByNativeSpeaker_fra();
        $this->testfilterByNativeSpeaker_null();
    }

    public function testfilterByNativeSpeaker_withLimit() {
        $this->Search->filterByLanguage('fra');
        $this->Search->filterByNativeSpeaker(true);
        $this->Search->setSphinxFilterArrayLimit(1);
        $expected = [
            'index' => ['fra_main_index', 'fra_delta_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'filter' => [['user_id', [7], true]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_oneTag() {
        $result = $this->Search->filterByTags(['OK']);
        $this->assertEquals(['OK'], $result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'filter' => [['tags_id', 2]]
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_twoTags() {
        $result = $this->Search->filterByTags(['OK', '@needs native check']);
        $this->assertEquals(['OK', '@needs native check'], $result);
        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'filter' => [['tags_id', 2], ['tags_id', 1]]
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_oneTag_oneInvalid() {
        $result = $this->Search->filterByTags(['nonexsistenttag']);
        $this->assertEmpty($result);

        $expected = ['index' => ['und_index'], 'matchMode' => SPH_MATCH_EXTENDED2];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_twoTags_oneInvalid() {
        $result = $this->Search->filterByTags(['OK', 'nonexsistenttag']);
        $this->assertEquals(['OK'], $result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'filter' => [['tags_id', 2]]
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_empty() {
        $result = $this->Search->filterByTags([]);
        $this->assertEmpty($result);

        $expected = ['index' => ['und_index'], 'matchMode' => SPH_MATCH_EXTENDED2];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_resets() {
        $this->testfilterByTags_oneTag();
        $this->testfilterByTags_empty();
    }

    public function testfilterByTags_sqlInjection() {
        $result = $this->Search->filterByTags(["'"]);
        $this->assertEmpty($result);

        $expected = ['index' => ['und_index'], 'matchMode' => SPH_MATCH_EXTENDED2];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslation_limit() {
        $result = $this->Search->filterByTranslation('limit');
        $this->assertEquals('limit', $result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'select' => '*, ANY(1 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslation_exclude() {
        $result = $this->Search->filterByTranslation('exclude');
        $this->assertEquals('exclude', $result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'select' => '*, ANY(1 FOR t IN trans) as filter',
            'filter' => [['filter', 0]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslation_invalid() {
        $result = $this->Search->filterByTranslation('invalid value');
        $this->assertNull($result);

        $expected = ['index' => ['und_index'], 'matchMode' => SPH_MATCH_EXTENDED2];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslation_null() {
        $result = $this->Search->filterByTranslation(null);
        $this->assertNull($result);

        $expected = ['index' => ['und_index'], 'matchMode' => SPH_MATCH_EXTENDED2];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslation_resets() {
        $this->testfilterByTranslation_limit();
        $this->testfilterByTranslation_null();
    }

    public function testfilterByTranslationAudio_true() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationAudio(true);
        $this->assertTrue($result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'select' => '*, ANY(t.a=1 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationAudio_false() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationAudio(false);
        $this->assertFalse($result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'select' => '*, ANY(t.a=0 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationAudio_null() {
        $result = $this->Search->filterByTranslationAudio(null);
        $this->assertNull($result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationLanguage_ainu() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationLanguage('ain');
        $this->assertEquals('ain', $result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'select' => "*, ANY(t.l='ain' FOR t IN trans) as filter",
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationLanguage_invalid() {
        $result = $this->Search->filterByTranslationLanguage('invalid value');
        $this->assertNull($result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationLanguage_null() {
        $result = $this->Search->filterByTranslationLanguage(null);
        $this->assertNull($result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationLanguage_resets() {
        $this->testfilterByTranslationLanguage_ainu();
        $this->testfilterByTranslationLanguage_null();
    }

    public function testfilterByTranslationLink_direct() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationLink('direct');
        $this->assertEquals('direct', $result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'select' => '*, ANY(t.d=1 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationLink_indirect() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationLink('indirect');
        $this->assertEquals('indirect', $result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'select' => '*, ANY(t.d=2 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationLink_invalid() {
        $result = $this->Search->filterByTranslationLink('invalid value');
        $this->assertNull($result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationLink_null() {
        $result = $this->Search->filterByTranslationLink(null);
        $this->assertNull($result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationLink_resets() {
        $this->testfilterByTranslationLink_direct();
        $this->testfilterByTranslationLink_null();
    }

    public function testfilterByTranslationOwnerId() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationOwnerId(4);
        $this->assertEquals(4, $result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'select' => '*, ANY(t.u=4 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationOwnerId_null() {
        $result = $this->Search->filterByTranslationOwnerId(null);
        $this->assertNull($result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationOrphanship_true() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationOrphanship(true);
        $this->assertTrue($result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'select' => '*, ANY(t.u=0 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationOrphanship_false() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationOrphanship(false);
        $this->assertFalse($result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'select' => '*, ANY(t.u<>0 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationOrphanship_null() {
        $result = $this->Search->filterByTranslationOrphanship(null);
        $this->assertNull($result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationOrphanship_resets() {
        $this->testfilterByTranslationOrphanship_true();
        $this->testfilterByTranslationOrphanship_null();
    }

    public function testfilterByTranslationCorrectness_true() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationCorrectness(true);
        $this->assertTrue($result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'select' => '*, ANY(t.c=0 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationCorrectness_false() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationCorrectness(false);
        $this->assertFalse($result);

        $expected = [
            'index' => ['und_index'],
            'matchMode' => SPH_MATCH_EXTENDED2,
            'select' => '*, ANY(t.c=1 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationCorrectness_null() {
        $result = $this->Search->filterByTranslationCorrectness(null);
        $this->assertNull($result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationCorrectness_resets() {
        $this->testfilterByTranslationCorrectness_true();
        $this->testfilterByTranslationCorrectness_null();
    }

    private function assertSortByRank($sort, $rank) {
        $expected = [
            'index' => ['und_index'],
            'query' => 'comme ci comme ça',
            'matchMode' => SPH_MATCH_EXTENDED2,
            'sortMode' => [SPH_SORT_EXTENDED => $sort],
            'rankingMode' => [SPH_RANK_EXPR => $rank],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    private function assertSortBy($sort) {
        $expected = [
            'index' => ['und_index'],
            'query' => '',
            'matchMode' => SPH_MATCH_EXTENDED2,
            'sortMode' => [SPH_SORT_EXTENDED => $sort],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testSortByRelevance_withNonEmptyQuery() {
        $this->Search->filterByQuery('comme ci comme ça');
        $this->assertEquals('relevance', $this->Search->sort('relevance'));
        $this->assertSortByRank('@rank DESC', '-text_len+top(lcs+exact_order*100)*100');
    }

    public function testSortByRelevance_withNonEmptyQuery_reversed() {
        $this->Search->filterByQuery('comme ci comme ça');
        $this->assertEquals('relevance', $this->Search->sort('relevance'));
        $this->Search->reverseSort(true);
        $this->assertSortByRank('@rank ASC', '-text_len+top(lcs+exact_order*100)*100');
    }

    public function testSortByRelevance_withEmptyQuery() {
        $this->Search->filterByQuery('');
        $this->assertEquals('relevance', $this->Search->sort('relevance'));
        $this->assertSortBy('text_len ASC');
    }

    public function testSortByRelevance_withEmptyQuery_reversed() {
        $this->Search->filterByQuery('');
        $this->assertEquals('relevance', $this->Search->sort('relevance'));
        $this->Search->reverseSort(true);
        $this->assertSortBy('text_len DESC');
    }

    public function testSortByWords_withNonEmptyQuery() {
        $this->Search->filterByQuery('comme ci comme ça');
        $this->assertEquals('words', $this->Search->sort('words'));
        $this->assertSortByRank('@rank DESC', '-text_len');
    }

    public function testSortByWords_withNonEmptyQuery_reversed() {
        $this->Search->filterByQuery('comme ci comme ça');
        $this->assertEquals('words', $this->Search->sort('words'));
        $this->Search->reverseSort(true);
        $this->assertSortByRank('@rank ASC', '-text_len');
    }

    public function testSortByWords_withEmptyQuery() {
        $this->Search->filterByQuery('');
        $this->assertEquals('words', $this->Search->sort('words'));
        $this->assertSortBy('text_len ASC');
    }

    public function testSortByWords_withEmptyQuery_reversed() {
        $this->Search->filterByQuery('');
        $this->assertEquals('words', $this->Search->sort('words'));
        $this->Search->reverseSort(true);
        $this->assertSortBy('text_len DESC');
    }

    public function testSortByCreated_withEmptyQuery() {
        $this->Search->filterByQuery('');
        $this->assertEquals('created', $this->Search->sort('created'));
        $this->assertSortBy('created DESC');
    }

    public function testSortByCreated_withEmptyQuery_reversed() {
        $this->Search->filterByQuery('');
        $this->assertEquals('created', $this->Search->sort('created'));
        $this->Search->reverseSort(true);
        $this->assertSortBy('created ASC');
    }

    public function testSortByCreated_withNonEmptyQuery() {
        $this->Search->filterByQuery('comme ci comme ça');
        $this->assertEquals('created', $this->Search->sort('created'));
        $this->assertSortByRank('@rank DESC', 'created');
    }

    public function testSortByCreated_withNonEmptyQuery_reversed() {
        $this->Search->filterByQuery('comme ci comme ça');
        $this->assertEquals('created', $this->Search->sort('created'));
        $this->Search->reverseSort(true);
        $this->assertSortByRank('@rank ASC', 'created');
    }

    public function testSortByModified_withEmptyQuery() {
        $this->Search->filterByQuery('');
        $this->assertEquals('modified', $this->Search->sort('modified'));
        $this->assertSortBy('modified DESC');
    }

    public function testSortByModified_withEmptyQuery_reversed() {
        $this->Search->filterByQuery('');
        $this->assertEquals('modified', $this->Search->sort('modified'));
        $this->Search->reverseSort(true);
        $this->assertSortBy('modified ASC');
    }

    public function testSortByModified_withNonEmptyQuery() {
        $this->Search->filterByQuery('comme ci comme ça');
        $this->assertEquals('modified', $this->Search->sort('modified'));
        $this->assertSortByRank('@rank DESC', 'modified');
    }

    public function testSortByModified_withNonEmptyQuery_reversed() {
        $this->Search->filterByQuery('comme ci comme ça');
        $this->assertEquals('modified', $this->Search->sort('modified'));
        $this->Search->reverseSort(true);
        $this->assertSortByRank('@rank ASC', 'modified');
    }

    public function testReverseSort_returnsTrue() {
        $result = $this->Search->reverseSort(true);
        $this->assertTrue($result);
    }

    public function testReverseSort_returnsFalse() {
        $result = $this->Search->reverseSort(false);
        $this->assertFalse($result);
    }

    public function testSortByRandom() {
        $this->Search->filterByQuery('');
        $this->assertEquals('random', $this->Search->sort('random'));
        $this->assertSortBy('@random');
    }

    public function testSortByInvalid() {
        $result = $this->Search->sort('invalidsortvalue');
        $this->assertEquals('', $result);

        $expected = ['index' => ['und_index'], 'matchMode' => SPH_MATCH_EXTENDED2];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testSortByEmpty() {
        $result = $this->Search->sort('');
        $this->assertEquals('', $result);

        $expected = ['index' => ['und_index'], 'matchMode' => SPH_MATCH_EXTENDED2];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testSortBy_resets() {
        $this->Search->sort('relevance');
        $this->testSortByEmpty();
    }

    public function exactQueryProvider() {
        return [
            ['="test"', 'test'],
            ['="\=test"', '=test'],
            ['="\\\\\(\)"', '\\()'],
        ];
    }

    /**
     * @dataProvider exactQueryProvider
     */
    public function testExactSearchQuery($expected, $query) {
        $this->assertEquals($expected, Search::exactSearchQuery($query));
    }
}
