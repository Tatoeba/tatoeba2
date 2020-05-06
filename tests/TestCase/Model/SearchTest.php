<?php
namespace App\Test\TestCase\Model;

include(APP.'Lib/SphinxClient.php'); // needed to get the constants
use App\Model\Search;
use Cake\TestSuite\TestCase;

class SearchTest extends TestCase
{
    public $fixtures = [
        'app.users',
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
        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function assertQuery($expectedQuery, $query) {
        $result = $this->Search->filterByQuery($query);
        $this->assertEquals($expectedQuery, $result);

        $expected = ['index' => ['und_index'], 'query' => $expectedQuery];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByQuery() {
        $this->assertQuery('comme ci comme ça', 'comme ci comme ça');
    }

    public function testfilterByQuery_empty() {
        $this->assertQuery('', '');
    }

    public function testfilterByQuery_nonBreakableSpace() {
        $this->assertQuery('ceci ; cela ; parce que', "ceci\u{a0}; cela\u{a0}; parce que");
    }

    public function testfilterByQuery_wideSpace() {
        $this->assertQuery('散りぬるを 我が世誰ぞ', "散りぬるを　我が世誰ぞ");
    }

    public function testfilterByLanguage_validLang() {
        $result = $this->Search->filterByLanguage('por');
        $this->assertEquals('por', $result);

        $expected = ['index' => ['por_main_index', 'por_delta_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByLanguage_und() {
        $result = $this->Search->filterByLanguage('und');
        $this->assertEquals('und', $result);

        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByLanguage_invalidLang() {
        $result = $this->Search->filterByLanguage('1234567890');
        $this->assertEquals('und', $result);

        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByLanguage_resets() {
        $this->testfilterByLanguage_validLang();
        $this->testfilterByLanguage_und();
    }

    public function testfilterByOwnerName_invalid() {
        $result = $this->Search->filterByOwnerName('userdoesnotexists');
        $this->assertFalse($result);
    }

    public function testfilterByOwnerName_valid() {
        $result = $this->Search->filterByOwnerName('contributor');
        $this->assertTrue($result);

        $expected = ['index' => ['und_index'], 'filter' => [['user_id', 4]]];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOwnerName_empty() {
        $expected = ['index' => ['und_index']];
        $result = $this->Search->filterByOwnerName('');
        $this->assertTrue($result);

        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOwnerName_resets() {
        $this->testfilterByOwnerName_valid();
        $this->testfilterByOwnerName_empty();
    }

    public function testfilterByOrphanship_yes() {
        $result = $this->Search->filterByOrphanship('yes');
        $this->assertEquals('yes', $result);

        $expected = ['index' => ['und_index'], 'filter' => [['user_id', 0, false]]];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOrphanship_no() {
        $result = $this->Search->filterByOrphanship('no');
        $this->assertEquals('no', $result);

        $expected = ['index' => ['und_index'], 'filter' => [['user_id', 0, true]]];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOrphanship_invalid() {
        $result = $this->Search->filterByOrphanship('invalid value');
        $this->assertEquals('', $result);

        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOrphanship_empty() {
        $result = $this->Search->filterByOrphanship('');
        $this->assertEquals('', $result);

        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByCorrectness_yes() {
        $result = $this->Search->filterByCorrectness('yes');
        $this->assertEquals('yes', $result);

        $expected = ['index' => ['und_index'], 'filter' => [['ucorrectness', 127, false]]];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByCorrectness_no() {
        $result = $this->Search->filterByCorrectness('no');
        $this->assertEquals('no', $result);

        $expected = ['index' => ['und_index'], 'filter' => [['ucorrectness', 127, true]]];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByCorrectness_invalid() {
        $result = $this->Search->filterByCorrectness('invalid value');
        $this->assertEquals('', $result);

        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByCorrectness_empty() {
        $result = $this->Search->filterByCorrectness('');
        $this->assertEquals('', $result);

        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByAudio_yes() {
        $result = $this->Search->filterByAudio('yes');
        $this->assertEquals('yes', $result);

        $expected = ['index' => ['und_index'], 'filter' => [['has_audio', 1]]];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByAudio_no() {
        $result = $this->Search->filterByAudio('no');
        $this->assertEquals('no', $result);

        $expected = ['index' => ['und_index'], 'filter' => [['has_audio', 0]]];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByAudio_invalid() {
        $result = $this->Search->filterByAudio('invalid value');
        $this->assertEquals('', $result);

        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByAudio_empty() {
        $result = $this->Search->filterByAudio('');
        $this->assertEquals('', $result);

        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_invalid() {
        $currentUserId = null;
        $listId = 999999999999;
        $result = $this->Search->filterByListId($listId, $currentUserId);
        $this->assertFalse($result);

        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_public() {
        $currentUserId = null;
        $listId = 2;
        $result = $this->Search->filterByListId($listId, $currentUserId);
        $this->assertTrue($result);

        $expected = ['index' => ['und_index'], 'filter' => [['lists_id', $listId]]];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_unlisted() {
        $currentUserId = null;
        $listId = 1;
        $result = $this->Search->filterByListId($listId, $currentUserId);
        $this->assertTrue($result);

        $expected = ['index' => ['und_index'], 'filter' => [['lists_id', $listId]]];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_private_isNotOwner() {
        $currentUserId = 1;
        $listId = 3;
        $result = $this->Search->filterByListId($listId, $currentUserId);
        $this->assertFalse($result);

        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_private_isOwner() {
        $currentUserId = 7;
        $listId = 3;
        $result = $this->Search->filterByListId($listId, $currentUserId);
        $this->assertTrue($result);

        $expected = ['index' => ['und_index'], 'filter' => [['lists_id', $listId]]];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_empty() {
        $currentUserId = null;
        $listId = '';
        $result = $this->Search->filterByListId($listId, $currentUserId);
        $this->assertTrue($result);

        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_resets() {
        $this->testfilterByListId_public();
        $this->testfilterByListId_empty();
    }

    public function testfilterByNativeSpeaker_fra() {
        $this->Search->filterByLanguage('fra');
        $result = $this->Search->filterByNativeSpeaker('yes');
        $this->assertEquals('yes', $result);

        $expected = [
            'index' => ['fra_main_index', 'fra_delta_index'],
            'filter' => [['user_id', [4, 3]]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByNativeSpeaker_invalid() {
        $this->Search->filterByLanguage('fra');
        $result = $this->Search->filterByNativeSpeaker('invalid value');
        $this->assertEquals('', $result);

        $expected = ['index' => ['fra_main_index', 'fra_delta_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByNativeSpeaker_empty() {
        $this->Search->filterByLanguage('fra');
        $result = $this->Search->filterByNativeSpeaker('');
        $this->assertEquals('', $result);

        $expected = ['index' => ['fra_main_index', 'fra_delta_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByNativeSpeaker_resets() {
        $this->testfilterByNativeSpeaker_fra();
        $this->testfilterByNativeSpeaker_empty();
    }

    public function testfilterByNativeSpeaker_withLimit() {
        $this->Search->filterByLanguage('fra');
        $this->Search->filterByNativeSpeaker('yes');
        $this->Search->setSphinxFilterArrayLimit(1);
        $expected = [
            'index' => ['fra_main_index', 'fra_delta_index'],
            'filter' => [['user_id', [7], true]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_oneTag() {
        $result = $this->Search->filterByTags(['OK']);
        $this->assertEquals(['OK'], $result);

        $expected = ['index' => ['und_index'], 'filter' => [['tags_id', 2]]];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_twoTags() {
        $result = $this->Search->filterByTags(['OK', '@needs native check']);
        $this->assertEquals(['OK', '@needs native check'], $result);
        $expected = [
            'index' => ['und_index'],
            'filter' => [['tags_id', 2], ['tags_id', 1]]
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_oneTag_oneInvalid() {
        $result = $this->Search->filterByTags(['nonexsistenttag']);
        $this->assertEmpty($result);

        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_twoTags_oneInvalid() {
        $result = $this->Search->filterByTags(['OK', 'nonexsistenttag']);
        $this->assertEquals(['OK'], $result);

        $expected = ['index' => ['und_index'], 'filter' => [['tags_id', 2]]];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_empty() {
        $result = $this->Search->filterByTags([]);
        $this->assertEmpty($result);

        $expected = ['index' => ['und_index']];
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

        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslation_limit() {
        $result = $this->Search->filterByTranslation('limit');
        $this->assertEquals('limit', $result);

        $expected = [
            'index' => ['und_index'],
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
            'select' => '*, ANY(1 FOR t IN trans) as filter',
            'filter' => [['filter', 0]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslation_invalid() {
        $result = $this->Search->filterByTranslation('invalid value');
        $this->assertEquals('', $result);

        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslation_empty() {
        $result = $this->Search->filterByTranslation('');
        $this->assertEquals('', $result);

        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslation_resets() {
        $this->testfilterByTranslation_limit();
        $this->testfilterByTranslation_empty();
    }

    public function testfilterByTranslationAudio_yes() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationAudio('yes');
        $this->assertEquals('yes', $result);

        $expected = [
            'index' => ['und_index'],
            'select' => '*, ANY(t.a=1 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationAudio_no() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationAudio('no');
        $this->assertEquals('no', $result);

        $expected = [
            'index' => ['und_index'],
            'select' => '*, ANY(t.a=0 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationAudio_invalid() {
        $result = $this->Search->filterByTranslationAudio('invalid value');
        $this->assertEquals('', $result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationAudio_empty() {
        $result = $this->Search->filterByTranslationAudio('');
        $this->assertEquals('', $result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationLanguage_ainu() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationLanguage('ain');
        $this->assertEquals('ain', $result);

        $expected = [
            'index' => ['und_index'],
            'select' => "*, ANY(t.l='ain' FOR t IN trans) as filter",
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationLanguage_invalid() {
        $result = $this->Search->filterByTranslationLanguage('invalid value');
        $this->assertEquals('und', $result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationLanguage_empty() {
        $result = $this->Search->filterByTranslationLanguage('');
        $this->assertEquals('und', $result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationLanguage_resets() {
        $this->testfilterByTranslationLanguage_ainu();
        $this->testfilterByTranslationLanguage_empty();
    }

    public function testfilterByTranslationLink_direct() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationLink('direct');
        $this->assertEquals('direct', $result);

        $expected = [
            'index' => ['und_index'],
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
            'select' => '*, ANY(t.d=2 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationLink_invalid() {
        $result = $this->Search->filterByTranslationLink('invalid value');
        $this->assertEquals('', $result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationLink_empty() {
        $result = $this->Search->filterByTranslationLink('');
        $this->assertEquals('', $result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationLink_resets() {
        $this->testfilterByTranslationLink_direct();
        $this->testfilterByTranslationLink_empty();
    }

    public function testfilterByTranslationOwnerName_valid() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationOwnerName('contributor');
        $this->assertTrue($result);

        $expected = [
            'index' => ['und_index'],
            'select' => '*, ANY(t.u=4 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationOwnerName_invalid() {
        $result = $this->Search->filterByTranslationOwnerName('userdoesnotexists');
        $this->assertFalse($result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationOwnerName_empty() {
        $result = $this->Search->filterByTranslationOwnerName('');
        $this->assertTrue($result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationOwnerName_resets() {
        $this->testfilterByTranslationOwnerName_valid();
        $this->testfilterByTranslationOwnerName_empty();
    }

    public function testfilterByTranslationOrphanship_yes() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationOrphanship('yes');
        $this->assertEquals('yes', $result);

        $expected = [
            'index' => ['und_index'],
            'select' => '*, ANY(t.u=0 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationOrphanship_no() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationOrphanship('no');
        $this->assertEquals('no', $result);

        $expected = [
            'index' => ['und_index'],
            'select' => '*, ANY(t.u<>0 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationOrphanship_invalid() {
        $result = $this->Search->filterByTranslationOrphanship('invalid value');
        $this->assertEquals('', $result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationOrphanship_empty() {
        $result = $this->Search->filterByTranslationOrphanship('');
        $this->assertEquals('', $result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationOrphanship_resets() {
        $this->testfilterByTranslationOrphanship_yes();
        $this->testfilterByTranslationOrphanship_empty();
    }

    public function testfilterByTranslationCorrectness_yes() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationCorrectness('yes');
        $this->assertEquals('yes', $result);

        $expected = [
            'index' => ['und_index'],
            'select' => '*, ANY(t.c=0 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationCorrectness_no() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationCorrectness('no');
        $this->assertEquals('no', $result);

        $expected = [
            'index' => ['und_index'],
            'select' => '*, ANY(t.c=1 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationCorrectness_invalid() {
        $result = $this->Search->filterByTranslationCorrectness('invalid value');
        $this->assertEquals('', $result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationCorrectness_empty() {
        $result = $this->Search->filterByTranslationCorrectness('');
        $this->assertEquals('', $result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationCorrectness_resets() {
        $this->testfilterByTranslationCorrectness_yes();
        $this->testfilterByTranslationCorrectness_empty();
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
        $this->assertEquals('yes',       $this->Search->reverseSort('yes'));
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
        $this->assertEquals('yes', $this->Search->reverseSort('yes'));
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
        $this->assertEquals('yes',   $this->Search->reverseSort('yes'));
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
        $this->assertEquals('yes',   $this->Search->reverseSort('yes'));
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
        $this->assertEquals('yes',     $this->Search->reverseSort('yes'));
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
        $this->assertEquals('yes',     $this->Search->reverseSort('yes'));
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
        $this->assertEquals('yes',      $this->Search->reverseSort('yes'));
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
        $this->assertEquals('yes',      $this->Search->reverseSort('yes'));
        $this->assertSortByRank('@rank ASC', 'modified');
    }

    public function testSortByRandom() {
        $this->Search->filterByQuery('');
        $this->assertEquals('random', $this->Search->sort('random'));
        $this->assertSortBy('@random');
    }

    public function testSortByInvalid() {
        $result = $this->Search->sort('invalidsortvalue');
        $this->assertEquals('', $result);

        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testSortByEmpty() {
        $result = $this->Search->sort('');
        $this->assertEquals('', $result);

        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testSortBy_resets() {
        $this->Search->sort('relevance');
        $this->testSortByEmpty();
    }

    public function testReverseSort_resets() {
        $this->Search->reverseSort('yes');
        $this->assertEquals('', $this->Search->reverseSort(''));
        $this->testSortByRelevance_withEmptyQuery();
    }

    public function testGetSearchableLists_asGuest() {
        $searcher = null;
        $include = '';
        $expected = [
            ['id' => 5, 'user_id' => 1, 'name' => 'Collaborative list'],
            ['id' => 6, 'user_id' => 7, 'name' => 'Inactive list'],
            ['id' => 2, 'user_id' => 7, 'name' => 'Public list'],
        ];
        $result = $this->Search->getSearchableLists($include, $searcher);

        $this->assertEquals($expected, $result->enableHydration(false)->toArray());
    }

    public function testGetSearchableLists_asGuest_withUnlisted() {
        $searcher = null;
        $include = 1;
        $expected = [
            ['id' => 5, 'user_id' => 1, 'name' => 'Collaborative list'],
            ['id' => 6, 'user_id' => 7, 'name' => 'Inactive list'],
            ['id' => 2, 'user_id' => 7, 'name' => 'Public list'],
            ['id' => 1, 'user_id' => 7, 'name' => 'Interesting French sentences'],
        ];
        $result = $this->Search->getSearchableLists($include, $searcher);

        $this->assertEquals($expected, $result->enableHydration(false)->toArray());
    }

    public function testGetSearchableLists_asUser() {
        $searcher = 7;
        $include = '';
        $expected = [
            ['id' => 5, 'user_id' => 1, 'name' => 'Collaborative list'],
            ['id' => 6, 'user_id' => 7, 'name' => 'Inactive list'],
            ['id' => 1, 'user_id' => 7, 'name' => 'Interesting French sentences'],
            ['id' => 3, 'user_id' => 7, 'name' => 'Private list'],
            ['id' => 2, 'user_id' => 7, 'name' => 'Public list'],
        ];
        $result = $this->Search->getSearchableLists($include, $searcher);

        $this->assertEquals($expected, $result->enableHydration(false)->toArray());
    }

    public function testGetSearchableLists_asUser_withUnlisted() {
        $searcher = 3;
        $include = 1;
        $expected = [
            ['id' => 5, 'user_id' => 1, 'name' => 'Collaborative list'],
            ['id' => 6, 'user_id' => 7, 'name' => 'Inactive list'],
            ['id' => 2, 'user_id' => 7, 'name' => 'Public list'],
            ['id' => 1, 'user_id' => 7, 'name' => 'Interesting French sentences'],
        ];
        $result = $this->Search->getSearchableLists($include, $searcher);

        $this->assertEquals($expected, $result->enableHydration(false)->toArray());
    }
}
