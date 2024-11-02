<?php
namespace App\Test\TestCase\Model;

include_once(APP.'Lib/SphinxClient.php'); // needed to get the constants
use App\Model\Exception\InvalidValueException;
use App\Model\Search;
use App\Model\Search\TagsFilter;
use App\Model\Search\OrphanFilter;
use App\Model\Search\OwnersFilter;
use App\Model\Search\WordCountFilter;
use App\Model\Search\TranslationCountFilter;
use App\Model\Search\TranslationLangFilter;
use Cake\TestSuite\TestCase;

class SearchTest extends TestCase
{
    public $fixtures = [
        'app.sentences_lists',
        'app.users_languages',
        'app.tags',
        'app.users',
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

    private function makeSphinxParams($add = []) {
        return array_merge(
            [
                'index' => ['und_index'],
                'matchMode' => SPH_MATCH_EXTENDED2,
                'select' => '*',
            ],
            $add
        );
    }

    public function testWithoutFilters() {
        $expected = $this->makeSphinxParams();
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function assertQuery($expectedQuery, $query) {
        $result = $this->Search->filterByQuery($query);
        $this->assertEquals($expectedQuery, $result);

        $expected = $this->makeSphinxParams(['query' => $expectedQuery]);
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
        $this->Search->filterByLanguage(['por']);

        $expected = $this->makeSphinxParams([
            'index' => ['por_main_index', 'por_delta_index']
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByLanguage_empty() {
        $this->Search->filterByLanguage([]);

        $expected = $this->makeSphinxParams();
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByLanguage_multiLang() {
        $this->Search->filterByLanguage(['por', 'spa']);

        $expected = $this->makeSphinxParams([
            'index' => ['por_main_index', 'por_delta_index', 'spa_main_index', 'spa_delta_index']
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByLanguage_und() {
        try {
            $this->Search->filterByLanguage(['und']);
            $this->fail("'und' language did not generate InvalidValueException");
        } catch (InvalidValueException $e) {
            $this->assertTrue(true);
        }

        $expected = $this->makeSphinxParams();
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByLanguage_invalidLang() {
        try {
            $this->Search->filterByLanguage(['1234567890']);
            $this->fail('Invalid language did not generate InvalidValueException');
        } catch (InvalidValueException $e) {
            $this->assertTrue(true);
        }

        $expected = $this->makeSphinxParams();
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByLanguage_multiLang_withInvalid() {
        try {
            $this->Search->filterByLanguage(['npi', '1234567890', 'spa']);
            $this->fail('Invalid language did not generate InvalidValueException');
        } catch (InvalidValueException $e) {
            $this->assertTrue(true);
        }

        $expected = $this->makeSphinxParams();
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByLanguage_resets() {
        $this->testfilterByLanguage_validLang();
        $this->testfilterByLanguage_empty();
    }

    public function testfilterByOwners_oneUser() {
        $this->Search->setFilter((new OwnersFilter())->anyOf(['contributor']));

        $expected = $this->makeSphinxParams([
            'filter' => [['user_id', [4], false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOwners_twoUsers() {
        $this->Search->setFilter((new OwnersFilter())->anyOf(['contributor', 'admin']));

        $expected = $this->makeSphinxParams([
            'filter' => [['user_id', [4, 1], false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOwners_exclude_oneUser() {
        $this->Search->setFilter((new OwnersFilter())->not()->anyOf(['contributor']));

        $expected = $this->makeSphinxParams([
            'filter' => [['user_id', [4], true]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOrphanship_true() {
        $this->Search->setFilter(new OrphanFilter(true));

        $expected = $this->makeSphinxParams([
            'filter' => [['user_id', [0], false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOrphanship_false() {
        $this->Search->setFilter(new OrphanFilter(false));

        $expected = $this->makeSphinxParams([
            'filter' => [['user_id', [0], true]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOrphanship_null() {
        $this->Search->unsetFilter(OrphanFilter::class);

        $expected = $this->makeSphinxParams();
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOrphanship_resets() {
        $this->testfilterByOrphanship_false();
        $this->testfilterByOrphanship_null();
    }

    public function testfilterByCorrectness_true() {
        $result = $this->Search->filterByCorrectness(true);
        $this->assertTrue($result);

        $expected = $this->makeSphinxParams([
            'filter' => [['ucorrectness', 127, false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByCorrectness_false() {
        $result = $this->Search->filterByCorrectness(false);
        $this->assertFalse($result);

        $expected = $this->makeSphinxParams([
            'filter' => [['ucorrectness', 127, true]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByCorrectness_null() {
        $result = $this->Search->filterByCorrectness(null);
        $this->assertNull($result);

        $expected = $this->makeSphinxParams();
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByAudio_true() {
        $result = $this->Search->filterByAudio(true);
        $this->assertTrue($result);

        $expected = $this->makeSphinxParams([
            'filter' => [['has_audio', 1]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByAudio_false() {
        $result = $this->Search->filterByAudio(false);
        $this->assertFalse($result);

        $expected = $this->makeSphinxParams([
            'filter' => [['has_audio', 0]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByAudio_null() {
        $result = $this->Search->filterByAudio(null);
        $this->assertNull($result);

        $expected = $this->makeSphinxParams();
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_invalid() {
        $currentUserId = null;
        $listId = 999999999999;
        $result = $this->Search->filterByListId($listId, $currentUserId);
        $this->assertFalse($result);

        $expected = $this->makeSphinxParams();
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_public() {
        $currentUserId = null;
        $listId = 2;
        $result = $this->Search->filterByListId($listId, $currentUserId);
        $this->assertTrue($result);

        $expected = $this->makeSphinxParams([
            'filter' => [['lists_id', $listId]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_unlisted() {
        $currentUserId = null;
        $listId = 1;
        $result = $this->Search->filterByListId($listId, $currentUserId);
        $this->assertTrue($result);

        $expected = $this->makeSphinxParams([
            'filter' => [['lists_id', $listId]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_private_isNotOwner() {
        $currentUserId = 1;
        $listId = 3;
        $result = $this->Search->filterByListId($listId, $currentUserId);
        $this->assertFalse($result);

        $expected = $this->makeSphinxParams();
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_private_isOwner() {
        $currentUserId = 7;
        $listId = 3;
        $result = $this->Search->filterByListId($listId, $currentUserId);
        $this->assertTrue($result);

        $expected = $this->makeSphinxParams([
            'filter' => [['lists_id', $listId]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_empty() {
        $currentUserId = null;
        $listId = '';
        $result = $this->Search->filterByListId($listId, $currentUserId);
        $this->assertTrue($result);

        $expected = $this->makeSphinxParams();
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_resets() {
        $this->testfilterByListId_public();
        $this->testfilterByListId_empty();
    }

    public function testfilterByNativeSpeaker_fra() {
        $this->Search->filterByLanguage(['fra']);
        $result = $this->Search->filterByNativeSpeaker(true);
        $this->assertTrue($result);

        $expected = $this->makeSphinxParams([
            'index' => ['fra_main_index', 'fra_delta_index'],
            'filter' => [['user_id', [4, 3]]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByNativeSpeaker_null() {
        $this->Search->filterByLanguage(['fra']);
        $result = $this->Search->filterByNativeSpeaker(null);
        $this->assertNull($result);

        $expected = $this->makeSphinxParams([
            'index' => ['fra_main_index', 'fra_delta_index'],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByNativeSpeaker_multiLang() {
        $this->Search->filterByLanguage(['fra', 'frm']);
        $result = $this->Search->filterByNativeSpeaker(true);
        $this->assertTrue($result);

        $expected = $this->makeSphinxParams([
            'index' => ['fra_main_index', 'fra_delta_index', 'frm_main_index', 'frm_delta_index'],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByNativeSpeaker_resets() {
        $this->testfilterByNativeSpeaker_fra();
        $this->testfilterByNativeSpeaker_null();
    }

    public function testfilterByNativeSpeaker_withLimit() {
        $this->Search->filterByLanguage(['fra']);
        $this->Search->filterByNativeSpeaker(true);
        $this->Search->setSphinxFilterArrayLimit(1);
        $expected = $this->makeSphinxParams([
            'index' => ['fra_main_index', 'fra_delta_index'],
            'filter' => [['user_id', [7], true]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByWordCount_count_invalid() {
        try {
            $this->Search->setFilter((new WordCountFilter())->anyOf(["'"]));
            $this->Search->asSphinx();
            $this->fail("\"'\" word count did not generate InvalidValueException");
        } catch (InvalidValueException $e) {
            $this->assertTrue(true);
        }
    }

    public function testfilterByWordCount_range_invalid() {
        try {
            $this->Search->setFilter((new WordCountFilter())->anyOf(['0-3-6']));
            $this->Search->asSphinx();
            $this->fail("\"'\" word count did not generate InvalidValueException");
        } catch (InvalidValueException $e) {
            $this->assertTrue(true);
        }
    }

    public function testfilterByWordCount_eq_1() {
        $this->Search->setFilter((new WordCountFilter())->anyOf(['1']));

        $expected = $this->makeSphinxParams([
            'select' => '*, text_len = 1 as WordCountFilter',
            'filter' => [['WordCountFilter', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByWordCount_le_10() {
        $this->Search->setFilter((new WordCountFilter())->anyOf(['-10']));

        $expected = $this->makeSphinxParams([
            'select' => '*, text_len <= 10 as WordCountFilter',
            'filter' => [['WordCountFilter', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByWordCount_ge_8() {
        $this->Search->setFilter((new WordCountFilter())->anyOf(['8-']));

        $expected = $this->makeSphinxParams([
            'select' => '*, text_len >= 8 as WordCountFilter',
            'filter' => [['WordCountFilter', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByWordCount_between_0_and_10() {
        $this->Search->setFilter((new WordCountFilter())->anyOf(['0-10']));

        $expected = $this->makeSphinxParams([
            'select' => '*, (text_len >= 0 and text_len <= 10) as WordCountFilter',
            'filter' => [['WordCountFilter', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByWordCount_full_range() {
        try {
            $this->Search->setFilter((new WordCountFilter())->anyOf(['-']));
            $result = $this->Search->asSphinx();
            $this->fail("'-' range did not generate InvalidValueException");
        } catch (InvalidValueException $e) {
            $this->assertTrue(true);
        }
    }

    public function testfilterByWordCount_exclude_range() {
        $this->Search->setFilter((new WordCountFilter())->not()->anyOf(['7-10']));

        $expected = $this->makeSphinxParams([
            'select' => '*, not (text_len >= 7 and text_len <= 10) as WordCountFilter',
            'filter' => [['WordCountFilter', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByWordCount_multirange_OR() {
        $this->Search->setFilter((new WordCountFilter())->anyOf(['0-2', '4-']));

        $expected = $this->makeSphinxParams([
            'select' => '*, ((text_len >= 0 and text_len <= 2) or text_len >= 4) as WordCountFilter',
            'filter' => [['WordCountFilter', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByWordCount_multirange_AND() {
        $this->Search->setFilter((new WordCountFilter())->anyOf(['0-10', '15'])->and()->not()->anyOf(['4']));

        $expected = $this->makeSphinxParams([
            'select' => '*, (((text_len >= 0 and text_len <= 10) or text_len = 15) and not (text_len = 4)) as WordCountFilter',
            'filter' => [['WordCountFilter', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_oneTag() {
        $this->Search->setFilter((new TagsFilter())->anyOf(['OK']));

        $expected = $this->makeSphinxParams([
            'filter' => [['tags_id', [2], false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_twoTags_AND() {
        $this->Search->setFilter((new TagsFilter())->anyOf(['OK'])->and()->anyOf(['@needs native check']));
        $expected = $this->makeSphinxParams([
            'filter' => [['tags_id', [2], false], ['tags_id', [1], false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_twoTags_OR() {
        $this->Search->setFilter((new TagsFilter())->anyOf(['OK', '@needs native check']));
        $expected = $this->makeSphinxParams([
            'filter' => [['tags_id', [2, 1], false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_oneTag_exclude() {
        $this->Search->setFilter((new TagsFilter())->not()->anyOf(['OK']));
        $expected = $this->makeSphinxParams([
            'filter' => [['tags_id', [2], true]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_oneTag_oneInvalid() {
        try {
            $this->Search->setFilter((new TagsFilter())->anyOf(['nonexsistenttag']));
            $this->Search->asSphinx();
            $this->fail("'nonexsistenttag' tag did not generate InvalidValueException");
        } catch (InvalidValueException $e) {
            $this->assertTrue(true);
        }
    }

    public function testfilterByTags_empty() {
        $this->Search->setFilter(new TagsFilter());

        $expected = $this->makeSphinxParams();
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_resets() {
        $this->testfilterByTags_oneTag();
        $this->testfilterByTags_empty();
    }

    public function testfilterByTags_sqlInjection() {
        try {
            $this->Search->setFilter((new TagsFilter())->anyOf(["'"]));
            $this->Search->asSphinx();
            $this->fail("\"'\" tag did not generate InvalidValueException");
        } catch (InvalidValueException $e) {
            $this->assertTrue(true);
        }
    }

    public function testfilterByTranslation_limit() {
        $result = $this->Search->filterByTranslation('limit');
        $this->assertEquals('limit', $result);

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(1 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationCount() {
        $this->Search->setTranslationFilter((new TranslationCountFilter())->anyOf([0]));

        $expected = $this->makeSphinxParams([
            'select' => '*, (length(trans) = 0) as tf',
            'filter' => [['tf', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationCount_exclude() {
        $this->Search->setTranslationFilter((new TranslationCountFilter())->anyOf([0]));
        $this->Search->getTranslationFilters()->setExclude(true);

        $expected = $this->makeSphinxParams([
            'select' => '*, (length(trans) = 0) as tf',
            'filter' => [['tf', 0]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationAudio_true() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationAudio(true);
        $this->assertTrue($result);

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(t.a=1 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationAudio_false() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationAudio(false);
        $this->assertFalse($result);

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(t.a=0 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationAudio_null() {
        $result = $this->Search->filterByTranslationAudio(null);
        $this->assertNull($result);

        $this->testfilterByTranslation_limit();
    }

    public function testfilterByTranslationLanguage_ainu() {
        $this->Search->setTranslationFilter((new TranslationLangFilter())->anyOf(['ain']));

        $expected = $this->makeSphinxParams([
            'select' => "*, ANY(t.l='ain' FOR t IN trans) as tf",
            'filter' => [['tf', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationLanguage_multilang() {
        $this->Search->setTranslationFilter((new TranslationLangFilter())->anyOf(['ain', 'pol']));

        $expected = $this->makeSphinxParams([
            'select' => "*, ANY((t.l='ain' | t.l='pol') FOR t IN trans) as tf",
            'filter' => [['tf', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationLanguage_multilang_exclude() {
        $this->Search->setTranslationFilter((new TranslationLangFilter())->not()->anyOf(['ain', 'pol']));

        $expected = $this->makeSphinxParams([
            'select' => "*, ANY(not (t.l='ain' | t.l='pol') FOR t IN trans) as tf",
            'filter' => [['tf', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationLanguage_invalid() {
        try {
            $this->Search->setTranslationFilter((new TranslationLangFilter())->anyOf(['invalid value']));
            $this->fail("'invalid value' language did not generate InvalidValueException");
        } catch (InvalidValueException $e) {
            $this->assertTrue(true);
        }
    }

    public function testfilterByTranslationLink_direct() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationLink('direct');
        $this->assertEquals('direct', $result);

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(t.d=1 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationLink_indirect() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationLink('indirect');
        $this->assertEquals('indirect', $result);

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(t.d=2 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ]);
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

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(t.u=4 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ]);
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

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(t.u=0 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationOrphanship_false() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationOrphanship(false);
        $this->assertFalse($result);

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(t.u<>0 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ]);
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

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(t.c=0 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationCorrectness_false() {
        $this->Search->filterByTranslation('limit');
        $result = $this->Search->filterByTranslationCorrectness(false);
        $this->assertFalse($result);

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(t.c=1 FOR t IN trans) as filter',
            'filter' => [['filter', 1]],
        ]);
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
        $expected = $this->makeSphinxParams([
            'query' => 'comme ci comme ça',
            'sortMode' => [SPH_SORT_EXTENDED => $sort],
            'rankingMode' => [SPH_RANK_EXPR => $rank],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    private function assertSortBy($sort) {
        $expected = $this->makeSphinxParams([
            'query' => '',
            'sortMode' => [SPH_SORT_EXTENDED => $sort],
        ]);
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

    public function testSortByRandom_withoutSeed_withEmptyQuery() {
        $this->Search->filterByQuery('');
        $this->Search->setRandSeed(null);
        $this->assertEquals('random', $this->Search->sort('random'));
        $this->assertSortBy('RAND()*16777216 DESC, id DESC');
    }

    public function testSortByRandom_withoutSeed_withEmptyQuery_reversed() {
        $this->Search->filterByQuery('');
        $this->Search->setRandSeed(null);
        $this->assertEquals('random', $this->Search->sort('random'));
        $this->Search->reverseSort(true);
        $this->assertSortBy('RAND()*16777216 ASC, id ASC');
    }

    public function testSortByRandom_withoutSeed_withNonEmptyQuery() {
        $this->Search->filterByQuery('comme ci comme ça');
        $this->Search->setRandSeed(null);
        $this->assertEquals('random', $this->Search->sort('random'));
        $this->assertSortByRank('@rank DESC, id DESC', 'RAND()*16777216');
    }

    public function testSortByRandom_withoutSeed_withNonEmptyQuery_reversed() {
        $this->Search->filterByQuery('comme ci comme ça');
        $this->Search->setRandSeed(null);
        $this->Search->reverseSort(true);
        $this->assertEquals('random', $this->Search->sort('random'));
        $this->assertSortByRank('@rank ASC, id ASC', 'RAND()*16777216');
    }

    public function testSortByRandom_withSeed_withEmptyQuery() {
        $this->Search->filterByQuery('');
        $this->Search->setRandSeed(123456789);
        $this->assertEquals('random', $this->Search->sort('random'));
        $this->assertSortBy('RAND(123456789)*16777216 DESC, id DESC');
    }

    public function testSortByRandom_withSeed_withEmptyQuery_reversed() {
        $this->Search->filterByQuery('');
        $this->Search->setRandSeed(123456789);
        $this->assertEquals('random', $this->Search->sort('random'));
        $this->Search->reverseSort(true);
        $this->assertSortBy('RAND(123456789)*16777216 ASC, id ASC');
    }

    public function testSortByRandom_withSeed_withNonEmptyQuery() {
        $this->Search->filterByQuery('comme ci comme ça');
        $this->Search->setRandSeed(123456789);
        $this->assertEquals('random', $this->Search->sort('random'));
        $this->assertSortByRank('@rank DESC, id DESC', 'RAND(123456789)*16777216');
    }

    public function testSortByRandom_withSeed_withNonEmptyQuery_reversed() {
        $this->Search->filterByQuery('comme ci comme ça');
        $this->Search->setRandSeed(123456789);
        $this->assertEquals('random', $this->Search->sort('random'));
        $this->Search->reverseSort(true);
        $this->assertSortByRank('@rank ASC, id ASC', 'RAND(123456789)*16777216');
    }

    public function testRandSeed_resets() {
        $this->Search->setRandSeed(12345);
        $this->testSortByRandom_withoutSeed_withEmptyQuery();
    }

    public function testSortByInvalid() {
        $result = $this->Search->sort('invalidsortvalue');
        $this->assertEquals('', $result);

        $expected = $this->makeSphinxParams();
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testSortByEmpty() {
        $result = $this->Search->sort('');
        $this->assertEquals('', $result);

        $expected = $this->makeSphinxParams();
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
