<?php
namespace App\Test\TestCase\Model;

include_once(APP.'Lib/SphinxClient.php'); // needed to get the constants
use App\Model\Exception\InvalidAndOperatorException;
use App\Model\Exception\InvalidFilterUsageException;
use App\Model\Exception\InvalidNotOperatorException;
use App\Model\Exception\InvalidValueException;
use App\Model\Search;
use App\Model\Search\CursorFilter;
use App\Model\Search\HasAudioFilter;
use App\Model\Search\IsOrphanFilter;
use App\Model\Search\IsNativeFilter;
use App\Model\Search\IsUnapprovedFilter;
use App\Model\Search\LangFilter;
use App\Model\Search\ListFilter;
use App\Model\Search\TagFilter;
use App\Model\Search\OriginFilter;
use App\Model\Search\OwnerFilter;
use App\Model\Search\WordCountFilter;
use App\Model\Search\TranslationCountFilter;
use App\Model\Search\TranslationFilterGroup;
use App\Model\Search\TranslationHasAudioFilter;
use App\Model\Search\TranslationLangFilter;
use App\Model\Search\TranslationIsDirectFilter;
use App\Model\Search\TranslationIsOrphanFilter;
use App\Model\Search\TranslationIsUnapprovedFilter;
use App\Model\Search\TranslationOwnerFilter;
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
        $this->Search->setFilter((new LangFilter())->anyOf(['por']));

        $expected = $this->makeSphinxParams([
            'index' => ['por_main_index', 'por_delta_index']
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByLanguage_empty() {
        $this->Search->setFilter((new LangFilter())->anyOf([]));

        $expected = $this->makeSphinxParams();
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByLanguage_multiLang() {
        $this->Search->setFilter((new LangFilter())->anyOf(['por', 'spa']));

        $expected = $this->makeSphinxParams([
            'index' => ['por_main_index', 'por_delta_index', 'spa_main_index', 'spa_delta_index']
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByLanguage_und() {
        try {
            $this->Search->setFilter((new LangFilter())->anyOf(['und']));
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
            $this->Search->setFilter((new LangFilter())->anyOf(['1234567890']));
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
            $this->Search->setFilter((new LangFilter())->anyOf(['npi', '1234567890', 'spa']));
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

    public function testfilterByOwner_oneUser() {
        $this->Search->setFilter((new OwnerFilter())->anyOf(['contributor']));

        $expected = $this->makeSphinxParams([
            'filter' => [['user_id', [4], false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOwner_oneUser_isCaseInsensitive() {
        $this->Search->setFilter((new OwnerFilter())->anyOf(['Contributor']));

        $expected = $this->makeSphinxParams([
            'filter' => [['user_id', [4], false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOwner_twoUsers() {
        $this->Search->setFilter((new OwnerFilter())->anyOf(['contributor', 'admin']));

        $expected = $this->makeSphinxParams([
            'filter' => [['user_id', [4, 1], false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOwner_exclude_oneUser() {
        $this->Search->setFilter((new OwnerFilter())->not()->anyOf(['contributor']));

        $expected = $this->makeSphinxParams([
            'filter' => [['user_id', [4], true]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOriginUnknown() {
        $this->Search->setFilter((new OriginFilter())->anyOf([OriginFilter::ORIGIN_UNKNOWN]));

        $expected = $this->makeSphinxParams([
            'filter' => [['origin_known', false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOriginKnown() {
        $this->Search->setFilter((new OriginFilter())->anyOf([OriginFilter::ORIGIN_KNOWN]));

        $expected = $this->makeSphinxParams([
            'filter' => [['origin_known', true]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByAddedAsOriginal() {
        $this->Search->setFilter((new OriginFilter())->anyOf([OriginFilter::ORIGIN_ORIGINAL]));

        $expected = $this->makeSphinxParams([
            'filter' => [['origin_known', true], ['is_original', true]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByAddedAsTranslation() {
        $this->Search->setFilter((new OriginFilter())->anyOf([OriginFilter::ORIGIN_TRANSLATION]));

        $expected = $this->makeSphinxParams([
            'filter' => [['origin_known', true], ['is_original', false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOrigin_invalid() {
        try {
            $this->Search->setFilter((new OriginFilter())->anyOf(['invalid']));
            $this->fail("origin 'invalid' did not generate InvalidValueException");
        } catch (InvalidValueException $e) {
            $this->assertTrue(true);
        }
    }

    public function testfilterByOrigin_two_values() {
        try {
            $this->Search->setFilter((new OriginFilter())->anyOf([OriginFilter::ORIGIN_KNOWN, OriginFilter::ORIGIN_UNKNOWN]));
            $this->fail("two origin values did not generate InvalidValueException");
        } catch (InvalidValueException $e) {
            $this->assertTrue(true);
        }
    }

    public function testfilterByOrphanship_true() {
        $this->Search->setFilter(new IsOrphanFilter(true));

        $expected = $this->makeSphinxParams([
            'filter' => [['user_id', [0], false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOrphanship_false() {
        $this->Search->setFilter(new IsOrphanFilter(false));

        $expected = $this->makeSphinxParams([
            'filter' => [['user_id', [0], true]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOrphanship_null() {
        $this->Search->unsetFilter(IsOrphanFilter::class);

        $expected = $this->makeSphinxParams();
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOrphanship_resets() {
        $this->testfilterByOrphanship_false();
        $this->testfilterByOrphanship_null();
    }

    public function testfilterByCorrectness_true() {
        $this->Search->setFilter(new IsUnapprovedFilter(true));

        $expected = $this->makeSphinxParams([
            'filter' => [['ucorrectness', [127], false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByCorrectness_false() {
        $this->Search->setFilter(new IsUnapprovedFilter(false));

        $expected = $this->makeSphinxParams([
            'filter' => [['ucorrectness', [127], true]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByCorrectness_null() {
        $this->Search->unsetFilter(IsUnapprovedFilter::class);

        $expected = $this->makeSphinxParams();
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByAudio_true() {
        $this->Search->setFilter(new HasAudioFilter(true));

        $expected = $this->makeSphinxParams([
            'filter' => [['has_audio', [1], false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByAudio_false() {
        $this->Search->setFilter(new HasAudioFilter(false));

        $expected = $this->makeSphinxParams([
            'filter' => [['has_audio', [1], true]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByAudio_null() {
        $this->Search->unsetFilter(HasAudioFilter::class);

        $expected = $this->makeSphinxParams();
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function assertInvalidListId($currentUserId, $listId) {
        try {
            $this->Search->setFilter((new ListFilter($currentUserId))->anyOf([$listId]));
            $this->Search->asSphinx();
            $this->fail("list id '$listId' did not generate InvalidValueException");
        } catch (InvalidValueException $e) {
            $this->assertTrue(true);
        }
    }

    public function testfilterByListId_unknown() {
        $currentUserId = null;
        $listId = 999999999999;
        $this->assertInvalidListId($currentUserId, $listId);
    }

    public function testfilterByListId_invalid() {
        $currentUserId = null;
        $listId = 'invalid';
        $this->assertInvalidListId($currentUserId, $listId);
    }

    public function testfilterByListId_public() {
        $currentUserId = null;
        $listId = 2;
        $this->Search->setFilter((new ListFilter($currentUserId))->anyOf([$listId]));

        $expected = $this->makeSphinxParams([
            'filter' => [['lists_id', [$listId], false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_unlisted() {
        $currentUserId = null;
        $listId = 1;
        $this->Search->setFilter((new ListFilter($currentUserId))->anyOf([$listId]));

        $expected = $this->makeSphinxParams([
            'filter' => [['lists_id', [$listId], false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_private_isNotOwner() {
        $currentUserId = 1;
        $listId = 3;
        $this->assertInvalidListId($currentUserId, $listId);
    }

    public function testfilterByListId_private_isOwner() {
        $currentUserId = 7;
        $listId = 3;
        $this->Search->setFilter((new ListFilter($currentUserId))->anyOf([$listId]));

        $expected = $this->makeSphinxParams([
            'filter' => [['lists_id', [$listId], false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByListId_empty() {
        $currentUserId = null;
        $listId = '';
        $this->assertInvalidListId($currentUserId, $listId);
    }

    public function testfilterByListId_resets() {
        $this->testfilterByListId_public();
        $this->testfilterByListId_empty();
    }

    public function testfilterIsNativeSpeaker_true_fra() {
        $this->Search->setFilter((new LangFilter())->anyOf(['fra']));
        $this->Search->setFilter((new IsNativeFilter(true))->setSearch($this->Search));

        $expected = $this->makeSphinxParams([
            'index' => ['fra_main_index', 'fra_delta_index'],
            'filter' => [['user_id', [4, 3], false]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterIsNativeSpeaker_false_fra() {
        $this->Search->setFilter((new LangFilter())->anyOf(['fra']));
        $this->Search->setFilter((new IsNativeFilter(false))->setSearch($this->Search));

        $expected = $this->makeSphinxParams([
            'index' => ['fra_main_index', 'fra_delta_index'],
            'filter' => [['user_id', [4, 3], true]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterIsNativeSpeaker_true_butNoNativeSpeakers() {
        $this->Search->setFilter((new LangFilter())->anyOf(['fro']));
        $this->Search->setFilter((new IsNativeFilter(true))->setSearch($this->Search));

        $expected = $this->makeSphinxParams([
            'index' => ['fro_main_index', 'fro_delta_index'],
            'filter' => [['user_id', [-1], false]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterIsNativeSpeaker_false_butNoNativeSpeakers() {
        $this->Search->setFilter((new LangFilter())->anyOf(['fro']));
        $this->Search->setFilter((new IsNativeFilter(false))->setSearch($this->Search));

        $expected = $this->makeSphinxParams([
            'index' => ['fro_main_index', 'fro_delta_index'],
            'filter' => [['user_id', [-1], true]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterIsNativeSpeaker_no_setSearch() {
        $this->Search->setFilter(new IsNativeFilter());
        try {
            $this->Search->compile();
            $this->fail("IsNativeFilter did not generate excepted RuntimeException");
        } catch (\RuntimeException $e) {
            $this->assertEquals('Precondition failed: setSearch() was not called first', $e->getMessage());
        }
    }

    public function testfilterIsNativeSpeaker_unset() {
        $this->Search->setFilter((new LangFilter())->anyOf(['fra']));
        $this->Search->unsetFilter(IsNativeFilter::class);

        $expected = $this->makeSphinxParams([
            'index' => ['fra_main_index', 'fra_delta_index'],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterIsNativeSpeaker_resets() {
        $this->testfilterIsNativeSpeaker_true_fra();
        $this->testfilterIsNativeSpeaker_unset();
    }

    public function testfilterIsNativeSpeaker_withLimit_true() {
        $this->Search->setFilter((new LangFilter())->anyOf(['fra']));
        $filter = (new IsNativeFilter(true))->setSearch($this->Search);
        $filter->setSphinxFilterArrayLimit(1);
        $this->Search->setFilter($filter);
        $expected = $this->makeSphinxParams([
            'index' => ['fra_main_index', 'fra_delta_index'],
            'filter' => [['user_id', [7], true]],
        ]);

        $result = $this->Search->asSphinx();

        $this->assertEquals($expected, $result);
    }

    public function testfilterIsNativeSpeaker_withLimit_false() {
        $this->Search->setFilter((new LangFilter())->anyOf(['fra']));
        $filter = (new IsNativeFilter(false))->setSearch($this->Search);
        $filter->setSphinxFilterArrayLimit(1);
        $this->Search->setFilter($filter);
        $expected = $this->makeSphinxParams([
            'index' => ['fra_main_index', 'fra_delta_index'],
            'filter' => [['user_id', [4], true], ['user_id', [3], true]],
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
            $this->fail("\"0-3-6\" word count did not generate InvalidValueException");
        } catch (InvalidValueException $e) {
            $this->assertTrue(true);
        }
    }

    public function testfilterByWordCount_range_decreasing() {
        try {
            $this->Search->setFilter((new WordCountFilter())->anyOf(['6-3']));
            $this->Search->asSphinx();
            $this->fail("\"6-3\" word count did not generate InvalidValueException");
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

    public function testfilterByWordCount_range_equal_bounds() {
        $this->Search->setFilter((new WordCountFilter())->anyOf(['1-1']));

        $expected = $this->makeSphinxParams([
            'select' => '*, (text_len >= 1 and text_len <= 1) as WordCountFilter',
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
        $this->Search->setFilter((new TagFilter())->anyOf(['OK']));

        $expected = $this->makeSphinxParams([
            'filter' => [['tags_id', [2], false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_oneTag_isCaseInsensitive() {
        $this->Search->setFilter((new TagFilter())->anyOf(['ok']));

        $expected = $this->makeSphinxParams([
            'filter' => [['tags_id', [2], false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_twoTags_AND() {
        $this->Search->setFilter((new TagFilter())->anyOf(['OK'])->and()->anyOf(['@needs native check']));
        $expected = $this->makeSphinxParams([
            'filter' => [['tags_id', [2], false], ['tags_id', [1], false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_twoTags_OR() {
        $this->Search->setFilter((new TagFilter())->anyOf(['OK', '@needs native check']));
        $expected = $this->makeSphinxParams([
            'filter' => [['tags_id', [2, 1], false]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_oneTag_exclude() {
        $this->Search->setFilter((new TagFilter())->not()->anyOf(['OK']));
        $expected = $this->makeSphinxParams([
            'filter' => [['tags_id', [2], true]]
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTags_oneTag_oneInvalid() {
        try {
            $this->Search->setFilter((new TagFilter())->anyOf(['nonexsistenttag']));
            $this->Search->asSphinx();
            $this->fail("'nonexsistenttag' tag did not generate InvalidValueException");
        } catch (InvalidValueException $e) {
            $this->assertTrue(true);
        }
    }

    public function testfilterByTags_empty() {
        $this->Search->setFilter(new TagFilter());

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
            $this->Search->setFilter((new TagFilter())->anyOf(["'"]));
            $this->Search->asSphinx();
            $this->fail("\"'\" tag did not generate InvalidValueException");
        } catch (InvalidValueException $e) {
            $this->assertTrue(true);
        }
    }

    public function testfilterByTranslationCount_int() {
        $this->Search->setTranslationFilter((new TranslationCountFilter())->anyOf([0]));

        $expected = $this->makeSphinxParams([
            'select' => '*, (length(trans) = 0) as tf',
            'filter' => [['tf', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationCount_str() {
        $this->Search->setTranslationFilter((new TranslationCountFilter())->anyOf(['0']));

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

    public function testfilterByTranslationCount_one() {
        try {
            $this->Search->setTranslationFilter((new TranslationCountFilter())->anyOf([1]));
            $this->Search->asSphinx();
            $this->fail("'1' translation count did not generate InvalidValueException");
        } catch (InvalidValueException $e) {
            $this->assertTrue(true);
        }
    }

    public function testfilterByTranslationCount_two_zeros() {
        try {
            $this->Search->setTranslationFilter((new TranslationCountFilter())->anyOf([0, 0]));
            $this->Search->asSphinx();
            $this->fail("two '0' translation counts did not generate InvalidValueException");
        } catch (InvalidValueException $e) {
            $this->assertTrue(true);
        }
    }

    public function testfilterByTranslationCount_invalid() {
        try {
            $this->Search->setTranslationFilter((new TranslationCountFilter())->anyOf(['invalid']));
            $this->Search->asSphinx();
            $this->fail("'invalid' translation counts did not generate InvalidValueException");
        } catch (InvalidValueException $e) {
            $this->assertTrue(true);
        }
    }

    public function testfilterByTranslationAudio_true() {
        $this->Search->setTranslationFilter(new TranslationHasAudioFilter(true));

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(t.a=1 FOR t IN trans) as tf',
            'filter' => [['tf', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationAudio_false() {
        $this->Search->setTranslationFilter(new TranslationHasAudioFilter(false));

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(not (t.a=1) FOR t IN trans) as tf',
            'filter' => [['tf', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
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
        $this->Search->setTranslationFilter(new TranslationIsDirectFilter(true));

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(t.d=1 FOR t IN trans) as tf',
            'filter' => [['tf', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationLink_indirect() {
        $this->Search->setTranslationFilter(new TranslationIsDirectFilter(false));

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(not (t.d=1) FOR t IN trans) as tf',
            'filter' => [['tf', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationOwner() {
        $filter = new TranslationOwnerFilter();
        $this->Search->setTranslationFilter($filter->anyOf(['contributor']));

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(t.u=4 FOR t IN trans) as tf',
            'filter' => [['tf', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationOwner_isCaseInsensitive() {
        $filter = new TranslationOwnerFilter();
        $this->Search->setTranslationFilter($filter->anyOf(['Contributor']));

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(t.u=4 FOR t IN trans) as tf',
            'filter' => [['tf', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationOwner_multi() {
        $filter = new TranslationOwnerFilter();
        $this->Search->setTranslationFilter($filter->anyOf(['contributor', 'admin']));

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY((t.u=4 | t.u=1) FOR t IN trans) as tf',
            'filter' => [['tf', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationOwner_invalid() {
        $filter = new TranslationOwnerFilter();
        $this->Search->setTranslationFilter($filter->anyOf(['invalid']));
        try {
            $result = $this->Search->asSphinx();
            $this->fail("'invalid' for translation username filter did not generate InvalidValueException");
        } catch (InvalidValueException $e) {
            $this->assertTrue(true);
        }
    }

    public function testfilterByTranslationOrphanship_true() {
        $this->Search->setTranslationFilter(new TranslationIsOrphanFilter(true));

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(t.u=0 FOR t IN trans) as tf',
            'filter' => [['tf', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationOrphanship_false() {
        $this->Search->setTranslationFilter(new TranslationIsOrphanFilter(false));

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(not (t.u=0) FOR t IN trans) as tf',
            'filter' => [['tf', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationOrphanship_false_using_not() {
        $this->Search->setTranslationFilter((new TranslationIsOrphanFilter())->not());

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(not (t.u=0) FOR t IN trans) as tf',
            'filter' => [['tf', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationCorrectness_true() {
        $this->Search->setTranslationFilter(new TranslationIsUnapprovedFilter(true));

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(t.c=0 FOR t IN trans) as tf',
            'filter' => [['tf', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByTranslationCorrectness_false() {
        $this->Search->setTranslationFilter(new TranslationIsUnapprovedFilter(false));

        $expected = $this->makeSphinxParams([
            'select' => '*, ANY(not (t.c=0) FOR t IN trans) as tf',
            'filter' => [['tf', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testCursorFilter() {
        $this->Search->setFilter((new CursorFilter())->anyOf([123, 456])->setSearch($this->Search));
        $this->Search->sort('modified');

        $expected = $this->makeSphinxParams([
            'select' => '*, (modified <= 123 AND NOT (modified = 123 AND id >= 456)) as keyset',
            'filter' => [['keyset', 1]],
            'sortMode' => [SPH_SORT_EXTENDED => 'modified DESC, id DESC'],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testCursorFilter_reversed() {
        $this->Search->setFilter((new CursorFilter())->anyOf([123, 456])->setSearch($this->Search));
        $this->Search->sort('modified');
        $this->Search->reverseSort(true);

        $expected = $this->makeSphinxParams([
            'select' => '*, (modified >= 123 AND NOT (modified = 123 AND id <= 456)) as keyset',
            'filter' => [['keyset', 1]],
            'sortMode' => [SPH_SORT_EXTENDED => 'modified ASC, id ASC'],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testCursorFilter_withQuery() {
        $this->Search->setFilter((new CursorFilter())->anyOf([123, 456])->setSearch($this->Search));
        $this->Search->sort('modified');
        $this->Search->filterByQuery('hello world');

        $expected = $this->makeSphinxParams([
            'select' => '*, (WEIGHT() <= 123 AND NOT (WEIGHT() = 123 AND id >= 456)) as keyset',
            'filter' => [['keyset', 1]],
            'sortMode' => [SPH_SORT_EXTENDED => '@rank DESC, id DESC'],
            'query' => 'hello world',
            'rankingMode' => [SPH_RANK_EXPR => 'modified'],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testCursorFilter_noSortOrderDefined() {
        $this->Search->setFilter((new CursorFilter())->anyOf([123, 456])->setSearch($this->Search));

        try {
            $this->Search->asSphinx();
        } catch (InvalidFilterUsageException $e) {
            $this->assertEquals("No sort order defined", $e->getMessage());
            return;
        }
        $this->fail('InvalidFilterUsageException was not thrown');
    }

    public function testCursorFilter_noTwoValuesProvided() {
        $this->Search->setFilter((new CursorFilter())->anyOf([123])->setSearch($this->Search));
        $this->Search->sort('modified');

        try {
            $this->Search->asSphinx();
        } catch (InvalidValueException $e) {
            $this->assertEquals("Expected 2 value(s), got 1 instead", $e->getMessage());
            return;
        }
        $this->fail('InvalidValueException was not thrown');
    }

    public function testCursorFilter_nonIntegerProvided() {
        try {
            (new CursorFilter())->anyOf(['invalid']);
        } catch (InvalidValueException $e) {
            $this->assertEquals("'invalid' is not an integer", $e->getMessage());
            return;
        }
        $this->fail('InvalidValueException was not thrown');
    }

    public function testComputeCursor_withoutQuery() {
        $this->Search->sort('modified');
        $this->Search->setComputeCursor(true);
        $expected = $this->makeSphinxParams([
            'select' => "*, CONCAT(TO_STRING(modified), ',', TO_STRING(id)) as cursor",
            'sortMode' => [SPH_SORT_EXTENDED => 'modified DESC, id DESC'],
        ]);

        $result = $this->Search->asSphinx();

        $this->assertEquals($expected, $result);
    }

    public function testComputeCursor_withQuery() {
        $this->Search->sort('modified');
        $this->Search->setComputeCursor(true);
        $this->Search->filterByQuery('hello world');
        $expected = $this->makeSphinxParams([
            'select' => "*, CONCAT(TO_STRING(WEIGHT()), ',', TO_STRING(id)) as cursor",
            'sortMode' => [SPH_SORT_EXTENDED => '@rank DESC, id DESC'],
            'query' => 'hello world',
            'rankingMode' => [SPH_RANK_EXPR => 'modified'],
        ]);

        $result = $this->Search->asSphinx();

        $this->assertEquals($expected, $result);
    }

    public function testNestedFilterGroups() {
        $group0 = $this->Search->getTranslationFilters('0');
        $group0->getTranslationFilters('a')
               ->setFilter((new TranslationLangFilter())->anyOf(['eng']))
               ->setFilter(new TranslationIsDirectFilter(true));
        $group0->getTranslationFilters('b')
               ->setFilter((new TranslationLangFilter())->anyOf(['ita']))
               ->setFilter(new TranslationIsDirectFilter(false))
               ->setExclude(true);
        $group0->setExclude(true);

        $this->Search->getTranslationFilters('1')
                     ->setFilter(new TranslationIsUnapprovedFilter(false));

        $expected = $this->makeSphinxParams([
            'select' => "*, (ANY((t.l='eng' & t.d=1) FOR t IN trans) and not (ANY((t.l='ita' & not (t.d=1)) FOR t IN trans))) as tf0, ANY(not (t.c=0) FOR t IN trans) as tf1",
            'filter' => [['tf0', 0], ['tf1', 1]],
        ]);
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    private function assertFilterThrowsException($filtername, $op, $expectedException) {
        $filter = new $filtername();
        try {
            $filter->$op();
        } catch (\Exception $e) {
            $actual = get_class($e);
            $this->assertEquals($expectedException, $actual, "$filtername->$op() did not throw expected $expectedException (but throwed $actual instead)");
            return;
        }
        $this->fail("$filtername->$op() did not throw expected $expectedException");
    }

    public function filtersExceptionsAndProvider() {
        return [
            [HasAudioFilter::class],
            [IsNativeFilter::class],
            [IsOrphanFilter::class],
            [IsUnapprovedFilter::class],
            [TranslationCountFilter::class],
            [TranslationHasAudioFilter::class],
            [TranslationLangFilter::class],
            [TranslationIsDirectFilter::class],
            [TranslationIsOrphanFilter::class],
            [TranslationIsUnapprovedFilter::class],
            [TranslationOwnerFilter::class],
            [OriginFilter::class],
            [OwnerFilter::class],
        ];
    }

    /**
     * @dataProvider filtersExceptionsAndProvider
     */
    public function testFiltersExceptionsAnd($filtername) {
        $this->assertFilterThrowsException($filtername, 'and', InvalidAndOperatorException::class);
    }

    public function filtersExceptionsNotProvider() {
        return [
            [LangFilter::class],
            [OriginFilter::class],
        ];
    }

    /**
     * @dataProvider filtersExceptionsNotProvider
     */
    public function testFiltersExceptionsNot($filtername) {
        $this->assertFilterThrowsException($filtername, 'not', InvalidNotOperatorException::class);
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
        $this->assertSortByRank('@rank DESC, id DESC', '-text_len+top(lcs+exact_order*100)*100');
    }

    public function testSortByRelevance_withNonEmptyQuery_reversed() {
        $this->Search->filterByQuery('comme ci comme ça');
        $this->assertEquals('relevance', $this->Search->sort('relevance'));
        $this->Search->reverseSort(true);
        $this->assertSortByRank('@rank ASC, id ASC', '-text_len+top(lcs+exact_order*100)*100');
    }

    public function testSortByRelevance_withEmptyQuery() {
        $this->Search->filterByQuery('');
        $this->assertEquals('relevance', $this->Search->sort('relevance'));
        $this->assertSortBy('text_len ASC, id DESC');
    }

    public function testSortByRelevance_withEmptyQuery_reversed() {
        $this->Search->filterByQuery('');
        $this->assertEquals('relevance', $this->Search->sort('relevance'));
        $this->Search->reverseSort(true);
        $this->assertSortBy('text_len DESC, id ASC');
    }

    public function testSortByWords_withNonEmptyQuery() {
        $this->Search->filterByQuery('comme ci comme ça');
        $this->assertEquals('words', $this->Search->sort('words'));
        $this->assertSortByRank('@rank DESC, id DESC', '-text_len');
    }

    public function testSortByWords_withNonEmptyQuery_reversed() {
        $this->Search->filterByQuery('comme ci comme ça');
        $this->assertEquals('words', $this->Search->sort('words'));
        $this->Search->reverseSort(true);
        $this->assertSortByRank('@rank ASC, id ASC', '-text_len');
    }

    public function testSortByWords_withEmptyQuery() {
        $this->Search->filterByQuery('');
        $this->assertEquals('words', $this->Search->sort('words'));
        $this->assertSortBy('text_len ASC, id DESC');
    }

    public function testSortByWords_withEmptyQuery_reversed() {
        $this->Search->filterByQuery('');
        $this->assertEquals('words', $this->Search->sort('words'));
        $this->Search->reverseSort(true);
        $this->assertSortBy('text_len DESC, id ASC');
    }

    public function testSortByCreated_withEmptyQuery() {
        $this->Search->filterByQuery('');
        $this->assertEquals('created', $this->Search->sort('created'));
        $this->assertSortBy('created DESC, id DESC');
    }

    public function testSortByCreated_withEmptyQuery_reversed() {
        $this->Search->filterByQuery('');
        $this->assertEquals('created', $this->Search->sort('created'));
        $this->Search->reverseSort(true);
        $this->assertSortBy('created ASC, id ASC');
    }

    public function testSortByCreated_withNonEmptyQuery() {
        $this->Search->filterByQuery('comme ci comme ça');
        $this->assertEquals('created', $this->Search->sort('created'));
        $this->assertSortByRank('@rank DESC, id DESC', 'created');
    }

    public function testSortByCreated_withNonEmptyQuery_reversed() {
        $this->Search->filterByQuery('comme ci comme ça');
        $this->assertEquals('created', $this->Search->sort('created'));
        $this->Search->reverseSort(true);
        $this->assertSortByRank('@rank ASC, id ASC', 'created');
    }

    public function testSortByModified_withEmptyQuery() {
        $this->Search->filterByQuery('');
        $this->assertEquals('modified', $this->Search->sort('modified'));
        $this->assertSortBy('modified DESC, id DESC');
    }

    public function testSortByModified_withEmptyQuery_reversed() {
        $this->Search->filterByQuery('');
        $this->assertEquals('modified', $this->Search->sort('modified'));
        $this->Search->reverseSort(true);
        $this->assertSortBy('modified ASC, id ASC');
    }

    public function testSortByModified_withNonEmptyQuery() {
        $this->Search->filterByQuery('comme ci comme ça');
        $this->assertEquals('modified', $this->Search->sort('modified'));
        $this->assertSortByRank('@rank DESC, id DESC', 'modified');
    }

    public function testSortByModified_withNonEmptyQuery_reversed() {
        $this->Search->filterByQuery('comme ci comme ça');
        $this->assertEquals('modified', $this->Search->sort('modified'));
        $this->Search->reverseSort(true);
        $this->assertSortByRank('@rank ASC, id ASC', 'modified');
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

    public function testGetInternalSortOrder_nonEmptyQuery() {
        $this->Search->sort('relevance');
        $this->Search->filterByQuery('hello world');
        $this->Search->compile();
        $expected = [['@rank', false], ['id', false]];

        $result = $this->Search->getInternalSortOrder();

        $this->assertEquals($expected, $result);
    }

    public function testGetInternalSortOrder_nonEmptyQuery_reversed() {
        $this->Search->sort('relevance');
        $this->Search->reverseSort(true);
        $this->Search->filterByQuery('hello world');
        $this->Search->compile();
        $expected = [['@rank', true], ['id', true]];

        $result = $this->Search->getInternalSortOrder();

        $this->assertEquals($expected, $result);
    }

    public function testGetInternalSortOrder_emptyQuery() {
        $this->Search->sort('relevance');
        $this->Search->compile();
        $expected = [['text_len', true], ['id', false]];

        $result = $this->Search->getInternalSortOrder();

        $this->assertEquals($expected, $result);
    }

    public function testGetInternalSortOrder_emptyQuery_reversed() {
        $this->Search->sort('relevance');
        $this->Search->reverseSort(true);
        $this->Search->compile();
        $expected = [['text_len', false], ['id', true]];

        $result = $this->Search->getInternalSortOrder();

        $this->assertEquals($expected, $result);
    }
}
