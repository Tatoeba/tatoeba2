<?php

use App\Model\Search;
use App\Model\SearchApi;
use App\Model\Exception\InvalidValueException;

use App\Model\Search\CursorFilter;
use App\Model\Search\HasAudioFilter;
use App\Model\Search\IsNativeFilter;
use App\Model\Search\IsOrphanFilter;
use App\Model\Search\IsUnapprovedFilter;
use App\Model\Search\LangFilter;
use App\Model\Search\ListFilter;
use App\Model\Search\OriginFilter;
use App\Model\Search\OwnerFilter;
use App\Model\Search\TagFilter;
use App\Model\Search\TranslationCountFilter;
use App\Model\Search\TranslationFilterGroup;
use App\Model\Search\TranslationHasAudioFilter;
use App\Model\Search\TranslationIsDirectFilter;
use App\Model\Search\TranslationIsOrphanFilter;
use App\Model\Search\TranslationIsUnapprovedFilter;
use App\Model\Search\TranslationLangFilter;
use App\Model\Search\TranslationOwnerFilter;
use App\Model\Search\WordCountFilter;

use App\Test\TestCase\SearchMockTrait;
use Cake\Http\Exception\BadRequestException;
use Cake\TestSuite\TestCase;

class SearchApiTest extends TestCase
{
    use SearchMockTrait;

    public $fixtures = [
        'app.sentences_lists',
        'app.users_languages',
        'app.tags',
        'app.users',
    ];

    public function setUp() {
        parent::setUp();
        $returnedSentenceIds = [1, 2, 3];
        $this->enableMockedSearch($returnedSentenceIds);
        $this->SearchApi = new SearchApi();
    }

    public function tearDown() {
        parent::tearDown();
        unset($this->SearchApi);
    }

    public function filtersProvider() {
        return [
            'missing lang' => [
                [],
                new BadRequestException('Required parameter "lang" missing')
            ],
            'valid lang' => [
                [ 'lang' => 'epo' ],
                [ (new LangFilter())->anyOf(['epo']) ],
            ],
            'invalid lang' => [
                ['lang' => '123' ],
                new BadRequestException("Invalid value for parameter 'lang': Invalid language code '123'")
            ],
            'multiple lang parameters' => [
                [ 'lang' => ['epo', 'sun'] ],
                new BadRequestException("Invalid usage of parameter 'lang': cannot be provided multiple times")
            ],
            'negated lang' => [
                [ 'lang' => '!epo' ],
                new BadRequestException("Invalid usage of parameter 'lang': value cannot be negated with '!'")
            ],
            'multiple lang values' => [
                [ 'lang' => 'epo,sun' ],
                [ (new LangFilter())->anyOf(['epo', 'sun']) ],
            ],

            'multiple q parameters' => [
                [ 'lang' => 'epo', 'q' => ['foo', 'bar'] ],
                new BadRequestException("Invalid usage of parameter 'q': cannot be provided multiple times")
            ],

            'valid word_count' => [
                [ 'lang' => 'epo', 'word_count' => '5-' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new WordCountFilter())->anyOf(['5-']),
                ],
            ],
            'invalid word_count value' => [
                [ 'lang' => 'epo', 'word_count' => 'nan' ],
                new BadRequestException("Invalid value for parameter 'word_count': Invalid number: 'nan'")
            ],
            'infinite word_count' => [
                [ 'lang' => 'epo', 'word_count' => '-' ],
                new BadRequestException("Invalid value for parameter 'word_count': Invalid infinite range: '-'")
            ],
            'invalid word_count range' => [
                [ 'lang' => 'epo', 'word_count' => '10-5' ],
                new BadRequestException("Invalid value for parameter 'word_count': Invalid range: left number must be lower or equal to right number")
            ],
            'multiple word_count values' => [
                [ 'lang' => 'epo', 'word_count' => '1,10' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new WordCountFilter())->anyOf(['1', '10']),
                ],
            ],
            'multiple word_count parameters' => [
                [ 'lang' => 'epo', 'word_count' => ['5-', '-10'] ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new WordCountFilter())->anyOf(['5-'])->and()->anyOf(['-10']),
                ],
            ],
            'negated word_count' => [
                [ 'lang' => 'epo', 'word_count' => '!5-' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new WordCountFilter())->not()->anyOf(['5-']),
                ],
            ],

            'valid owner' => [
                [ 'lang' => 'epo', 'owner' => 'contributor' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new OwnerFilter())->anyOf(['contributor']),
                ],
            ],
            'invalid owner' => [
                [ 'lang' => 'epo', 'owner' => 'invalid' ],
                new BadRequestException("Invalid value for parameter 'owner': No such owner: 'invalid'")
            ],
            'multiple owner parameters' => [
                [ 'lang' => 'epo', 'owner' => ['contributor', 'admin'] ],
                new BadRequestException("Invalid usage of parameter 'owner': cannot be provided multiple times")
            ],
            'multiple owner values' => [
                [ 'lang' => 'epo', 'owner' => 'contributor,admin' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new OwnerFilter())->anyOf(['contributor', 'admin']),
                ],
            ],
            'negated owner' => [
                [ 'lang' => 'epo', 'owner' => '!contributor' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new OwnerFilter())->not()->anyOf(['contributor']),
                ],
            ],

            'valid is_orphan' => [
                [ 'lang' => 'epo', 'is_orphan' => 'yes' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    new IsOrphanFilter(true),
                ],
            ],
            'invalid is_orphan' => [
                [ 'lang' => 'epo', 'is_orphan' => 'invalid' ],
                new BadRequestException("Invalid value for parameter 'is_orphan': must be 'yes' or 'no'")
            ],
            'multiple is_orphan parameters' => [
                [ 'lang' => 'epo', 'is_orphan' => ['yes', 'yes'] ],
                new BadRequestException("Invalid usage of parameter 'is_orphan': cannot be provided multiple times")
            ],

            'valid is_unapproved' => [
                [ 'lang' => 'epo', 'is_unapproved' => 'no' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    new IsUnapprovedFilter(false),
                ],
            ],
            'invalid is_unapproved' => [
                [ 'lang' => 'epo', 'is_unapproved' => 'invalid' ],
                new BadRequestException("Invalid value for parameter 'is_unapproved': must be 'yes' or 'no'")
            ],
            'multiple is_unapproved parameters' => [
                [ 'lang' => 'epo', 'is_unapproved' => ['yes', 'yes'] ],
                new BadRequestException("Invalid usage of parameter 'is_unapproved': cannot be provided multiple times")
            ],

            'valid has_audio' => [
                [ 'lang' => 'epo', 'has_audio' => 'yes' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    new HasAudioFilter(true),
                ],
            ],
            'invalid has_audio' => [
                [ 'lang' => 'epo', 'has_audio' => 'invalid' ],
                new BadRequestException("Invalid value for parameter 'has_audio': must be 'yes' or 'no'")
            ],
            'multiple has_audio parameters' => [
                [ 'lang' => 'epo', 'has_audio' => ['yes', 'yes'] ],
                new BadRequestException("Invalid usage of parameter 'has_audio': cannot be provided multiple times")
            ],

            'valid tag' => [
                [ 'lang' => 'epo', 'tag' => 'OK' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new TagFilter())->anyOf(['OK']),
                ],
            ],
            'invalid tag' => [
                [ 'lang' => 'epo', 'tag' => 'invalid' ],
                new BadRequestException("Invalid value for parameter 'tag': No such tag: 'invalid'")
            ],
            'multiple tag parameters' => [
                [ 'lang' => 'epo', 'tag' => ['OK', '@needs native check'] ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new TagFilter())->anyOf(['OK'])->and()->anyOf(['@needs native check']),
                ],
            ],
            'multiple tag values' => [
                [ 'lang' => 'epo', 'tag' => ['OK,regional'] ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new TagFilter())->anyOf(['OK', 'regional']),
                ],
            ],
            'negated tag' => [
                [ 'lang' => 'epo', 'tag' => '!regional' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new TagFilter())->not()->anyOf(['regional']),
                ],
            ],

            'valid list' => [
                [ 'lang' => 'epo', 'list' => '1' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new ListFilter())->anyOf([1]),
                ],
            ],
            'invalid list' => [
                [ 'lang' => 'epo', 'list' => 'invalid' ],
                new BadRequestException("Invalid value for parameter 'list': Invalid list id: 'invalid'")
            ],
            'multiple list parameters' => [
                [ 'lang' => 'epo', 'list' => ['1', '2'] ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new ListFilter())->anyOf([1])->and()->anyOf([2]),
                ],
            ],
            'multiple list values' => [
                [ 'lang' => 'epo', 'list' => ['1,2'] ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new ListFilter())->anyOf([1, 2]),
                ],
            ],
            'negated list' => [
                [ 'lang' => 'epo', 'list' => '!2' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new ListFilter())->not()->anyOf([2]),
                ],
            ],

            'valid is_native' => [
                [ 'lang' => 'epo', 'is_native' => 'yes' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    new IsNativeFilter(true),
                ],
            ],
            'invalid is_native' => [
                [ 'lang' => 'epo', 'is_native' => 'invalid' ],
                new BadRequestException("Invalid value for parameter 'is_native': must be 'yes' or 'no'")
            ],
            'multiple is_native parameters' => [
                [ 'lang' => 'epo', 'is_native' => ['yes', 'yes'] ],
                new BadRequestException("Invalid usage of parameter 'is_native': cannot be provided multiple times")
            ],
            'is_native with multiple langs' => [
                [ 'lang' => 'epo,sun', 'is_native' => 'yes' ],
                new BadRequestException("Invalid usage of parameter 'is_native': must be used with a single language (multiple languages were provided to the language filter: epo sun)")
            ],

            'valid origin' => [
                [ 'lang' => 'epo', 'origin' => 'original' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new OriginFilter())->anyOf(['original']),
                ],
            ],
            'invalid origin' => [
                [ 'lang' => 'epo', 'origin' => 'invalid' ],
                new BadRequestException("Invalid value for parameter 'origin': Value must be one of: unknown, known, original, translation")
            ],
            'multiple origin values' => [
                [ 'lang' => 'epo', 'origin' => 'original,translation' ],
                new BadRequestException("Invalid value for parameter 'origin': Only a single value is accepted")
            ],
            'negated origin' => [
                [ 'lang' => 'epo', 'origin' => '!original' ],
                new BadRequestException("Invalid usage of parameter 'origin': value cannot be negated with '!'")
            ],
            'multiple origin parameters' => [
                [ 'lang' => 'epo', 'origin' => ['original', 'translation'] ],
                new BadRequestException("Invalid usage of parameter 'origin': cannot be provided multiple times")
            ],

            'valid trans:lang' => [
                [ 'lang' => 'epo', 'trans:lang' => 'sun' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new TranslationFilterGroup())->setFilter(
                        (new TranslationLangFilter())->anyOf(['sun'])
                    )
                ],
            ],
            'invalid trans:lang' => [
                [ 'lang' => 'epo', 'trans:lang' => 'invalid' ],
                new BadRequestException("Invalid value for parameter 'trans:lang': Invalid language code 'invalid'")
            ],
            'multiple trans:lang values' => [
                [ 'lang' => 'epo', 'trans:lang' => 'sun,vie' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new TranslationFilterGroup())->setFilter(
                        (new TranslationLangFilter())->anyOf(['sun', 'vie'])
                    )
                ]
            ],
            'negated trans:lang' => [
                [ 'lang' => 'epo', 'trans:lang' => '!sun' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new TranslationFilterGroup())->setFilter(
                        (new TranslationLangFilter())->not()->anyOf(['sun'])
                    )
                ]
            ],
            'multiple trans:lang parameters' => [
                [ 'lang' => 'epo', 'trans:lang' => ['sun', 'vie'] ],
                new BadRequestException("Invalid usage of parameter 'trans:lang': cannot be provided multiple times")
            ],

            'valid trans:is_direct' => [
                [ 'lang' => 'epo', 'trans:is_direct' => 'no' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new TranslationFilterGroup())->setFilter(new TranslationIsDirectFilter(false)),
                ],
            ],
            'invalid trans:is_direct' => [
                [ 'lang' => 'epo', 'trans:is_direct' => 'invalid' ],
                new BadRequestException("Invalid value for parameter 'trans:is_direct': must be 'yes' or 'no'")
            ],
            'multiple trans:is_direct parameters' => [
                [ 'lang' => 'epo', 'trans:is_direct' => ['yes', 'yes'] ],
                new BadRequestException("Invalid usage of parameter 'trans:is_direct': cannot be provided multiple times")
            ],

            'valid trans:owner' => [
                [ 'lang' => 'epo', 'trans:owner' => 'contributor' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new TranslationFilterGroup())->setFilter((new TranslationOwnerFilter())->anyOf(['contributor'])),
                ],
            ],
            'invalid trans:owner' => [
                [ 'lang' => 'epo', 'trans:owner' => 'invalid' ],
                new BadRequestException("Invalid value for parameter 'trans:owner': No such owner: 'invalid'")
            ],
            'multiple trans:owner parameters' => [
                [ 'lang' => 'epo', 'trans:owner' => ['contributor', 'admin'] ],
                new BadRequestException("Invalid usage of parameter 'trans:owner': cannot be provided multiple times")
            ],
            'multiple trans:owner values' => [
                [ 'lang' => 'epo', 'trans:owner' => 'contributor,admin' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new TranslationFilterGroup())->setFilter(
                        (new TranslationOwnerFilter())->anyOf(['contributor', 'admin'])
                    )
                ],
            ],
            'negated trans:owner' => [
                [ 'lang' => 'epo', 'trans:owner' => '!contributor' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new TranslationFilterGroup())->setFilter(
                        (new TranslationOwnerFilter())->not()->anyOf(['contributor'])
                    ),
                ],
            ],

            'valid trans:is_unapproved' => [
                [ 'lang' => 'epo', 'trans:is_unapproved' => 'yes' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new TranslationFilterGroup())->setFilter(new TranslationIsUnapprovedFilter(true)),
                ],
            ],
            'invalid trans:is_unapproved' => [
                [ 'lang' => 'epo', 'trans:is_unapproved' => 'invalid' ],
                new BadRequestException("Invalid value for parameter 'trans:is_unapproved': must be 'yes' or 'no'")
            ],
            'multiple trans:is_unapproved parameters' => [
                [ 'lang' => 'epo', 'trans:is_unapproved' => ['yes', 'yes'] ],
                new BadRequestException("Invalid usage of parameter 'trans:is_unapproved': cannot be provided multiple times")
            ],

            'valid trans:is_orphan' => [
                [ 'lang' => 'epo', 'trans:is_orphan' => 'no' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new TranslationFilterGroup())->setFilter(new TranslationIsOrphanFilter(false)),
                ],
            ],
            'invalid trans:is_orphan' => [
                [ 'lang' => 'epo', 'trans:is_orphan' => 'invalid' ],
                new BadRequestException("Invalid value for parameter 'trans:is_orphan': must be 'yes' or 'no'")
            ],
            'multiple trans:is_orphan parameters' => [
                [ 'lang' => 'epo', 'trans:is_orphan' => ['yes', 'yes'] ],
                new BadRequestException("Invalid usage of parameter 'trans:is_orphan': cannot be provided multiple times")
            ],

            'valid trans:has_audio' => [
                [ 'lang' => 'epo', 'trans:has_audio' => 'yes' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new TranslationFilterGroup())->setFilter(new TranslationHasAudioFilter(true)),
                ],
            ],
            'invalid trans:has_audio' => [
                [ 'lang' => 'epo', 'trans:has_audio' => 'invalid' ],
                new BadRequestException("Invalid value for parameter 'trans:has_audio': must be 'yes' or 'no'")
            ],
            'multiple trans:has_audio parameters' => [
                [ 'lang' => 'epo', 'trans:has_audio' => ['yes', 'yes'] ],
                new BadRequestException("Invalid usage of parameter 'trans:has_audio': cannot be provided multiple times")
            ],

            'valid trans:count' => [
                [ 'lang' => 'epo', 'trans:count' => '0' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new TranslationFilterGroup())->setFilter((new TranslationCountFilter())->anyOf(['0'])),
                ],
            ],
            'nonzero trans:count' => [
                [ 'lang' => 'epo', 'trans:count' => '1' ],
                new BadRequestException("Invalid value for parameter 'trans:count': Only a single value of 0 is allowed")
            ],
            'invalid trans:count' => [
                [ 'lang' => 'epo', 'trans:count' => 'invalid' ],
                new BadRequestException("Invalid value for parameter 'trans:count': Only a single value of 0 is allowed")
            ],
            'negated trans:count' => [
                [ 'lang' => 'epo', 'trans:count' => '!0' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new TranslationFilterGroup())->setFilter((new TranslationCountFilter())->not()->anyOf(['0'])),
                ],
            ],
            'multiple trans:count parameters' => [
                [ 'lang' => 'epo', 'trans:count' => ['0', '!0'] ],
                new BadRequestException("Invalid usage of parameter 'trans:count': cannot be provided multiple times")
            ],
            'multiple trans:count values' => [
                [ 'lang' => 'epo', 'trans:count' => '0,0' ],
                new BadRequestException("Invalid value for parameter 'trans:count': Only a single value of 0 is allowed")
            ],

            'multiple trans: prefixes' => [
                [ 'lang' => 'epo', 'trans:1:lang' => 'sun', 'trans:2:lang' => 'vie' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new TranslationFilterGroup('1'))->setFilter((new TranslationLangFilter())->anyOf(['sun'])),
                    (new TranslationFilterGroup('2'))->setFilter((new TranslationLangFilter())->anyOf(['vie'])),
                ],
            ],
            'invalid trans: prefix' => [
                [ 'lang' => 'epo', 'trans:a:lang' => 'sun' ],
                new BadRequestException("Invalid parameter 'trans:a:lang': 'a' is not a valid group name: it must consist of non-empty digits with optional exclamation mark prefix"),
            ],
            'negated trans: prefix' => [
                [ 'lang' => 'epo', 'trans:!1:lang' => 'sun', 'trans:!1:is_direct' => 'yes' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new TranslationFilterGroup('_1'))
                        ->setFilter((new TranslationLangFilter())->anyOf(['sun']))
                        ->setFilter(new TranslationIsDirectFilter(true))
                        ->setExclude(true),
                ],
            ],
            'special !trans: prefix' => [
                [ 'lang' => 'epo', '!trans:1:lang' => 'sun', '!trans:2:lang' => 'vie' ],
                [
                    (new LangFilter())->anyOf(['epo']),
                    (new TranslationFilterGroup('e'))
                        ->setFilter(
                            (new TranslationFilterGroup('1'))->setFilter(
                                (new TranslationLangFilter())->anyOf(['sun'])
                            )
                        )
                        ->setFilter(
                            (new TranslationFilterGroup('2'))->setFilter(
                                (new TranslationLangFilter())->anyOf(['vie'])
                            )
                        )
                        ->setExclude(true),
                ],
            ],

            'invalid parameter' => [
                [ 'lang' => 'epo', 'invalid' => 'blah' ],
                new BadRequestException("Unknown parameter 'invalid'"),
            ],
            'invalid trans: parameter' => [
                [ 'lang' => 'epo', 'trans:invalid' => 'blah' ],
                new BadRequestException("Unknown parameter 'trans:invalid': unknown suffix 'invalid'"),
            ],
            'invalid namespace in parameter' => [
                [ 'lang' => 'epo', 'invalid:lang' => 'sun' ],
                new BadRequestException("Unknown parameter 'invalid:lang'"),
            ],
            'empty trans: group name' => [
                [ 'lang' => 'epo', 'trans::lang' => 'sun' ],
                new BadRequestException("Invalid parameter 'trans::lang': group name cannot be empty"),
            ],
            'too many fields' => [
                [ 'lang' => 'epo', 'trans:1::lang' => 'sun' ],
                new BadRequestException("Unknown parameter 'trans:1::lang': unknown suffix ':lang'"),
            ],

            'invalid after parameter, non-integer' => [
                [ 'lang' => 'epo', 'sort' => 'words', 'after' => 'invalid' ],
                new BadRequestException("Invalid value for parameter 'after': 'invalid' is not an integer"),
            ],
            'invalid after parameter, only one value' => [
                [ 'lang' => 'epo', 'sort' => 'words', 'after' => '123' ],
                new BadRequestException("Invalid value for parameter 'after': Expected 2 value(s), got 1 instead"),
            ],
            'multiple after parameters' => [
                [ 'lang' => 'epo', 'sort' => 'words', 'after' => ['123', '456'] ],
                new BadRequestException("Invalid usage of parameter 'after': cannot be provided multiple times"),
            ],
            'negated after parameter' => [
                [ 'lang' => 'epo', 'sort' => 'words', 'after' => '!123,456' ],
                new BadRequestException("Invalid usage of parameter 'after': value cannot be negated with '!'"),
            ],
        ];
    }

    private function assertFiltersThrowException($filters, $exception) {
        $expectedName = get_class($exception);
        try {
            if (isset($filters['sort'])) {
                $this->SearchApi->consumeSort($filters);
            }
            $this->SearchApi->setFilters($filters);
        } catch(\Exception $actual) {
            $this->assertEquals($exception, $actual);
            return;
        }
        $this->fail("$expectedName was not thrown");
    }

    private function _buildSearchFromFilters($filters) {
        $search = new Search();
        foreach ($filters as $filter) {
            $search->setFilter($filter);
            if (method_exists($filter, 'setSearch')) {
                $filter->setSearch($search);
            }
        }
        return $search;
    }

    /**
     * @dataProvider filtersProvider()
     */
    public function testFilters($filters, $expected) {
        if ($expected instanceof \Exception) {
            $this->assertFiltersThrowException($filters, $expected);
        } else {
            $expectedSearch = $this->_buildSearchFromFilters($expected);
            $this->SearchApi->setFilters($filters);
            $this->assertEquals($expectedSearch->asSphinx(), $this->SearchApi->search->asSphinx());
        }
    }

    public function testQ() {
        $expectedSearch = new Search();
        $expectedSearch->filterByQuery('hello world');
        $expectedSearch->setFilter((new LangFilter())->anyOf(['epo']));

        $filters = ['lang' => 'epo', 'q' => 'hello world'];
        $this->SearchApi->setFilters($filters);

        $this->assertEquals($expectedSearch->asSphinx(), $this->SearchApi->search->asSphinx());
    }

    public function testConsumeSort_OK() {
        $expectedSearch = new Search();
        $expectedSearch->setFilter((new LangFilter())->anyOf(['epo']));
        $expectedSearch->sort('modified');
        $expectedSearch->setComputeCursor(true);

        $params = ['sort' => 'modified', 'lang' => 'epo'];
        $this->SearchApi->consumeSort($params);
        $this->SearchApi->setFilters($params);

        $this->assertEquals($expectedSearch->asSphinx(), $this->SearchApi->search->asSphinx());
    }

    public function testConsumeSort_reverse() {
        $expectedSearch = new Search();
        $expectedSearch->setFilter((new LangFilter())->anyOf(['epo']));
        $expectedSearch->sort('modified');
        $expectedSearch->reverseSort(true);
        $expectedSearch->setComputeCursor(true);

        $params = ['sort' => '-modified', 'lang' => 'epo'];
        $this->SearchApi->consumeSort($params);
        $this->SearchApi->setFilters($params);

        $this->assertEquals($expectedSearch->asSphinx(), $this->SearchApi->search->asSphinx());
    }

    public function testCursorFilter_OK() {
        $expectedSearch = new Search();
        $expectedSearch->setFilter((new LangFilter())->anyOf(['epo']));
        $expectedSearch->setFilter((new CursorFilter())->anyOf([123, 456])->setSearch($expectedSearch));
        $expectedSearch->sort('modified');
        $expectedSearch->reverseSort(true);
        $expectedSearch->setComputeCursor(true);

        $params = ['sort' => '-modified', 'lang' => 'epo', 'after' => '123,456'];
        $this->SearchApi->consumeSort($params);
        $this->SearchApi->setFilters($params);

        $this->assertEquals($expectedSearch->asSphinx(), $this->SearchApi->search->asSphinx());
    }

    public function sortFailureProvider() {
        return [
            'missing' => [null, new BadRequestException('Required parameter "sort" missing')],
            'empty'   => ['', new BadRequestException('Invalid value for parameter "sort"')],
            'invalid' => ['invalid', new BadRequestException('Invalid value for parameter "sort"')],
            'multiple params' => [
                ['created', 'modified'],
                new BadRequestException("Invalid usage of parameter 'sort': cannot be provided multiple times")
            ],
        ];
    }

    /**
     * @dataProvider sortFailureProvider()
     */
    public function testConsumeSortFailure($sort, $expected) {
        try {
            $params = ['sort' => $sort];
            $this->SearchApi->consumeSort($params);
        } catch (\Exception $actual) {
            $this->assertEquals($expected, $actual);
            return;
        }
        $this->fail(get_class($expected) . " was not thrown");
    }

    public function showTransProvider() {
        return [
            'absent'        => [ [],                              ['lang' => [], 'is_direct' => null] ],
            'multiple lang' => [ ['showtrans:lang' => 'sun,vie'], ['lang' => ['sun', 'vie'], 'is_direct' => null] ],
            'empty lang' => [
                ['showtrans:lang' => ''],
                new BadRequestException("Invalid value for parameter 'showtrans:lang': Invalid language code ''")
            ],
            'invalid lang' => [
                ['showtrans:lang' => 'invalid'],
                new BadRequestException("Invalid value for parameter 'showtrans:lang': Invalid language code 'invalid'")
            ],
            'multiple lang params' => [
                ['showtrans:lang' => ['sun', 'vie']],
                new BadRequestException("Invalid usage of parameter 'showtrans:lang': cannot be provided multiple times")
            ],
            'is_direct yes' => [ ['showtrans:is_direct' => 'yes'], ['lang' => [], 'is_direct' => true] ],
            'is_direct no'  => [ ['showtrans:is_direct' => 'no'],  ['lang' => [], 'is_direct' => false] ],
            'empty is_direct' => [
                ['showtrans:is_direct' => ''],
                new BadRequestException("Invalid usage of parameter 'showtrans:is_direct': must be 'yes' or 'no'")
            ],
            'invalid is_direct' => [
                ['showtrans:is_direct' => 'invalid'],
                new BadRequestException("Invalid usage of parameter 'showtrans:is_direct': must be 'yes' or 'no'")
            ],
            'multiple is_direct params' => [
                ['showtrans:is_direct' => ['yes', 'no']],
                new BadRequestException("Invalid usage of parameter 'showtrans:is_direct': cannot be provided multiple times")
            ],
        ];
    }

    /**
     * @dataProvider showTransProvider()
     */
    public function testConsumeShowTrans($params, $expected) {
        try {
            $result = $this->SearchApi->consumeShowTrans($params);
        } catch (\Exception $actual) {
            $this->assertEquals($expected, $actual);
            return;
        }

        if ($expected instanceOf \Exception) {
            $this->fail(get_class($expected) . " was not thrown");
        } else {
            $this->assertEquals($expected, $result);
            $this->assertFalse(isset($params['showtrans']));
        }
    }

    public function limitProvider() {
        return [
            'absent limit' => [ [], 200 ],
            'empty limit' => [
                ['limit' => ''],
                new BadRequestException("Invalid value for parameter 'limit': must be a positive integer")
            ],
            'invalid limit' => [
                ['limit' => 'invalid'],
                new BadRequestException("Invalid value for parameter 'limit': must be a positive integer")
            ],
            'negative limit' => [
                ['limit' => '-1'],
                new BadRequestException("Invalid value for parameter 'limit': must be a positive integer")
            ],
            'multiple limit params' => [
                ['limit' => ['100', '200']],
                new BadRequestException("Invalid usage of parameter 'limit': cannot be provided multiple times")
            ],
        ];
    }

    /**
     * @dataProvider limitProvider()
     */
    public function testConsumeInt_limit($params, $expected) {
        try {
            $result = $this->SearchApi->consumeInt('limit', $params, 200);
        } catch (\Exception $actual) {
            $this->assertEquals($expected, $actual);
            return;
        }

        if ($expected instanceOf \Exception) {
            $this->fail(get_class($expected) . " was not thrown");
        } else {
            $this->assertEquals($expected, $result);
            $this->assertFalse(isset($params['limit']));
        }
    }
}
