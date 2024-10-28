<?php

use App\Form\SentencesSearchForm;
use App\Model\Search\OrphanFilter;
use App\Model\Search\OwnersFilter;
use App\Model\Search\WordCountFilter;
use Cake\TestSuite\TestCase;

class SentencesSearchFormTest extends TestCase
{
    public $fixtures = [
        'app.users',
        'app.sentences_lists',
        'app.tags',
        'app.users_languages',
    ];

    public function setUp() {
        parent::setUp();
        $this->Form = new SentencesSearchForm();
        $this->Search = $this->createTestProxy(\App\Model\Search::class);
        $this->Form->setSearch($this->Search);
    }

    public function tearDown() {
        parent::tearDown();
        unset($this->Form);
    }

    public function testDefaultData() {
        $expected = [
            'query' => '',
            'to' => '',
            'from' => '',
            'unapproved' => 'no',
            'orphans' => 'no',
            'user' => '',
            'has_audio' => '',
            'word_count_min' => '1',
            'word_count_max' => '',
            'tags' => '',
            'list' => '',
            'native' => '',
            'trans_filter' => 'limit',
            'trans_to' => '',
            'trans_link' => '',
            'trans_has_audio' => '',
            'trans_unapproved' => '',
            'trans_orphan' => '',
            'trans_user' => '',
            'sort' => 'relevance',
            'sort_reverse' => '',
            'rand_seed' => '',
        ];
        $this->Form->setData([]);
        $this->assertEquals($expected, $this->Form->getData());
    }

    public function testUnknownParam() {
        $this->Form->setData(['unknown_param' => 'super strange']);
        $this->assertNull($this->Form->getData('unknown_param'));
    }

    public function searchParamsProvider() {
        return [
            [ ['query' => 'your proposal'],
              ['filterByQuery', 'your proposal' ],
              'your proposal'
            ],
            [ ['query' => '散りぬるを　我が世誰ぞ'],
              ['filterByQuery', '散りぬるを 我が世誰ぞ' ],
              '散りぬるを 我が世誰ぞ'
            ],
            [ ['query' => "ceci\u{a0}; cela\u{a0}"],
              ['filterByQuery', 'ceci ; cela ' ],
              'ceci ; cela '
            ],

            [ ['from' => 'ain'],         ['filterByLanguage', ['ain']        ], 'ain' ],
            [ ['from' => ''],            ['filterByLanguage', ['']           ], '' ],
            [ ['from' => 'invalidlang'], ['filterByLanguage', ['invalidlang']], '' ],

            [ ['to' => 'und'],     [], '' ],
            [ ['to' => 'none'],    [], 'none' ],
            [ ['to' => 'fra'],     [], 'fra' ],
            [ ['to' => ''],        [], '' ],
            [ ['to' => 'invalid'], [], '' ],

            [ ['unapproved' => 'yes'],     ['filterByCorrectness', true],  'yes' ],
            [ ['unapproved' => 'no'],      ['filterByCorrectness', false], 'no'  ],
            [ ['unapproved' => 'any'],     ['filterByCorrectness', null],  'any' ],
            [ ['unapproved' => 'invalid'], ['filterByCorrectness', null],  'any' ],
            [ ['unapproved' => ''],        ['filterByCorrectness', null],  'any' ],

            [ ['orphans' => 'yes'],     ['OrphanFilter' => new OrphanFilter(true)],  'yes' ],
            [ ['orphans' => 'no'],      ['OrphanFilter' => new OrphanFilter(false)], 'no'  ],
            [ ['orphans' => 'any'],     ['OrphanFilter' => null],                    'any' ],
            [ ['orphans' => 'invalid'], ['OrphanFilter' => null],                    'any' ],
            [ ['orphans' => ''],        ['OrphanFilter' => null],                    'any' ],

            [ ['user' => 'contributor'], ['OwnersFilter' => (new OwnersFilter())->anyOf(['contributor'])], 'contributor', 0 ],
            [ ['user' => 'invaliduser'], ['OwnersFilter' => new OwnersFilter()],                           '',            1 ],
            [ ['user' => ''],            ['OwnersFilter' => null],                                         '',            0 ],

            [ ['has_audio' => 'yes'],     ['filterByAudio', true],  'yes' ],
            [ ['has_audio' => 'no'],      ['filterByAudio', false], 'no'  ],
            [ ['has_audio' => 'invalid'], ['filterByAudio', null],  ''    ],
            [ ['has_audio' => ''],        ['filterByAudio', null],  ''    ],

            [ ['tags' => 'OK'],          ['setFilter', [['tags_id', [2], false]]], 'OK'    ],
            [ ['tags' => 'invalid tag'], ['setFilter', []],                        '',   1 ],
            [ ['tags' => 'OK,invalid'],  ['setFilter', [['tags_id', [2], false]]], 'OK', 1 ],

            [ ['list' => '2'],       ['filterByListId', 2, null],       '2'   ],
            [ ['list' => '9999999'], ['filterByListId', 9999999, null], '', 1 ],
            [ ['list' => ''],        ['filterByListId', null, null],    ''    ],
            [ ['list' => '3'],       ['filterByListId', 3, null],       '', 1 ],

            [ ['native' => 'yes'],     ['filterByNativeSpeaker', true],  'yes' ],
            [ ['native' => 'no'],      ['filterByNativeSpeaker', null],  ''    ],
            [ ['native' => 'invalid'], ['filterByNativeSpeaker', null],  ''    ],
            [ ['native' => ''],        ['filterByNativeSpeaker', null],  ''    ],

            [ ['word_count_min' => ''],        ['WordCountFilter' => null],                                           'any'],
            [ ['word_count_min' => '0'],       ['WordCountFilter' => (new WordCountFilter())->anyOf(['0-'])->and()],  '0'  ],
            [ ['word_count_min' => '01'],      ['WordCountFilter' => (new WordCountFilter())->anyOf(['1-'])->and()],  '1'  ],
            [ ['word_count_min' => '42'],      ['WordCountFilter' => (new WordCountFilter())->anyOf(['42-'])->and()], '42' ],
            [ ['word_count_min' => 'invalid'], ['WordCountFilter' => null],                                           'any'],

            [ ['word_count_max' => ''],        ['WordCountFilter' => (new WordCountFilter())->anyOf(['1-'])->and()],  ''   ],
            [ ['word_count_max' => '0'],       ['WordCountFilter' => (new WordCountFilter())->anyOf(['-0'])->and()
                                                                                            ->anyOf(['1-'])->and()],  '0'  ],
            [ ['word_count_max' => '01'],      ['WordCountFilter' => (new WordCountFilter())->anyOf(['-1'])->and()
                                                                                            ->anyOf(['1-'])->and()],  '1'  ],
            [ ['word_count_max' => '42'],      ['WordCountFilter' => (new WordCountFilter())->anyOf(['-42'])->and()
                                                                                            ->anyOf(['1-'])->and()],  '42' ],
            [ ['word_count_max' => 'invalid'], ['WordCountFilter' => (new WordCountFilter())->anyOf(['1-'])->and()],  ''   ],

            [ ['trans_filter' => 'exclude'],      ['filterByTranslation', 'exclude'], 'exclude' ],
            [ ['trans_filter' => 'invalidvalue'], ['filterByTranslation'], 'limit' ],

            [ ['trans_to' => 'ain'],     ['filterByTranslationLanguage', 'ain'    ], 'ain' ],
            [ ['trans_to' => ''],        ['filterByTranslationLanguage', ''       ], '' ],
            [ ['trans_to' => 'invalid'], ['filterByTranslationLanguage', 'invalid'], '' ],

            [ ['trans_link' => 'direct'],   ['filterByTranslationLink', 'direct'],  'direct'],
            [ ['trans_link' => 'indirect'], ['filterByTranslationLink', 'indirect'],'indirect'],
            [ ['trans_link' => ''],         ['filterByTranslationLink', ''],        ''],
            [ ['trans_link' => 'invalid'],  ['filterByTranslationLink', 'invalid'], ''],

            [ ['trans_has_audio' => 'yes'],     ['filterByTranslationAudio', true],  'yes' ],
            [ ['trans_has_audio' => 'no'],      ['filterByTranslationAudio', false], 'no'  ],
            [ ['trans_has_audio' => 'invalid'], ['filterByTranslationAudio', null],  ''    ],
            [ ['trans_has_audio' => ''],        ['filterByTranslationAudio', null],  ''    ],

            [ ['trans_unapproved' => 'yes'],     ['filterByTranslationCorrectness', true],  'yes' ],
            [ ['trans_unapproved' => 'no'],      ['filterByTranslationCorrectness', false], 'no'  ],
            [ ['trans_unapproved' => 'invalid'], ['filterByTranslationCorrectness', null],  ''    ],
            [ ['trans_unapproved' => ''],        ['filterByTranslationCorrectness', null],  ''    ],

            [ ['trans_orphan' => 'yes'],     ['filterByTranslationOrphanship', true],  'yes' ],
            [ ['trans_orphan' => 'no'],      ['filterByTranslationOrphanship', false], 'no'  ],
            [ ['trans_orphan' => 'invalid'], ['filterByTranslationOrphanship', null],  ''    ],
            [ ['trans_orphan' => ''],        ['filterByTranslationOrphanship', null],  ''    ],

            [ ['trans_user' => 'contributor'], ['filterByTranslationOwnerId', 4], 'contributor' ],
            [ ['trans_user' => 'invaliduser'], ['filterByTranslationOwnerId'],    '', 1 ],
            [ ['trans_user' => ''],            ['filterByTranslationOwnerId'],    ''    ],

            [ ['sort' => 'relevance'], ['sort', 'relevance'], 'relevance' ],
            [ ['sort' => 'words'],     ['sort', 'words'],     'words'     ],
            [ ['sort' => 'modified'],  ['sort', 'modified'],  'modified'  ],
            [ ['sort' => 'created'],   ['sort', 'created'],   'created'   ],
            [ ['sort' => 'random'],    ['sort', 'random'],    'random'    ],

            [ ['sort_reverse' => 'yes'],     ['reverseSort', true],  'yes' ],
            [ ['sort_reverse' => ''],        ['reverseSort', false], '' ],
            [ ['sort_reverse' => 'invalid'], ['reverseSort', false], '' ],

            [ ['rand_seed' => 'xrgU'],          ['setRandSeed',  1358022], 'xrgU' ],
            [ ['rand_seed' => '3-_a'],          ['setRandSeed', 14348255], '3-_a' ],
            [ ['rand_seed' => ''],              ['setRandSeed',     null], ''     ],
            [ ['rand_seed' => 'longer string'], ['setRandSeed', 14715286], 'long' ],
            [ ['rand_seed' => 'sml'],           ['setRandSeed',     null], ''     ],
            [ ['rand_seed' => '.!"@'],          ['setRandSeed',     null], ''     ],
        ];
    }

    /**
     * @dataProvider searchParamsProvider
     */
    public function testSearchParams($getParams, $method, $getParamReturned, $ignored = 0) {
        if (count($method) == 1 && is_int(key($method))) { // is list array
            $this->Search->expects($this->never())
                         ->method($method[0]);
        } elseif (is_int(key($method))) { // is list array but with more than 1 element
            $methodName = array_shift($method);
            $with = array_map(
                function ($expected) {
                    return $this->callback(function($param) use ($expected) {
                        return $expected === $param;
                    });
                },
                $method
            );
            $this->Search->expects($this->once())
                         ->method($methodName)
                         ->with(...$with);
        }

        $this->Form->setData($getParams);

        if (!is_int(key($method))) { // is associative array
            $allfilters = $this->Search->getFilters();
            foreach ($method as $filterkey => $expected) {
                if (is_null($expected)) {
                    $this->assertFalse(isset($allfilters->{$filterkey}), "$filterkey was set");
                } elseif (isset($allfilters->{$filterkey})) {
                    $result = $allfilters->{$filterkey};
                    $this->assertEquals($expected->compile(), $result->compile(), "$filterkey does not contain expected filter");
                } else {
                    $this->fail("$filterkey was not set");
                }
            }
        }
        if (!is_array($getParamReturned)) {
            $getParamReturned = [key($getParams) => $getParamReturned];
        }
        foreach ($getParamReturned as $getParam => $expectedValue) {
            $this->assertEquals($expectedValue, $this->Form->getData()[$getParam]);
        }

        $this->assertCount($ignored, $this->Form->getIgnoredFields(), 'ignored fields count');
    }

    public function testSearchParamToIsCopiedToTransTo_fra() {
        $this->Form->setData(['to' => 'fra']);
        $result = $this->Form->getData();
        $this->assertEquals('fra', $result['to']);
        $this->assertEquals('fra', $result['trans_to']);
    }

    public function testSearchParamToIsCopiedToTransTo_none() {
        $this->Form->setData(['to' => 'none']);
        $result = $this->Form->getData();
        $this->assertEquals('none', $result['to']);
        $this->assertEquals('',  $result['trans_to']);
    }

    public function testSearchParamToIsCopiedToTransTo_empty() {
        $this->Form->setData(['to' => '']);
        $result = $this->Form->getData();
        $this->assertEquals('', $result['to']);
        $this->assertEquals('', $result['trans_to']);
    }

    public function testSearchParamToIsCopiedToTransTo_invalid() {
        $this->Form->setData(['to' => 'invalid']);
        $result = $this->Form->getData();
        $this->assertEquals('', $result['to']);
        $this->assertEquals('', $result['trans_to']);
    }

    public function testTransFilter_limitWithoutTransFilters() {
        $this->Search->expects($this->never())
                     ->method('filterByTranslation');
        $this->Form->setData(['trans_filter' => 'limit']);
        $this->assertEquals('limit', $this->Form->getData()['trans_filter']);
    }

    public function testTransFilter_limitWithTranslationFilters() {
        $this->Search->expects($this->once())
                     ->method('filterByTranslation')
                     ->with($this->equalTo('limit'));
        $this->Form->setData(['trans_filter' => 'limit', 'trans_to' => 'hun']);
        $this->assertEquals('limit', $this->Form->getData()['trans_filter']);
    }

    private function assertMethodCalledWith($stub, $methodName, $expectedParams) {
        $stub->expects($this->exactly(count($expectedParams)))
             ->method($methodName)
             ->with($this->callback(
                 function ($param) use (&$expectedParams) {
                     return array_shift($expectedParams) === $param;
                 }
             ));
    }

    public function testSort_invalid() {
        $this->assertMethodCalledWith($this->Search, 'sort', ['invalid', 'relevance']);
        $this->Form->setData(['sort' => 'invalid']);
        $this->assertEquals('relevance', $this->Form->getData()['sort']);
    }

    public function testSort_empty() {
        $this->assertMethodCalledWith($this->Search, 'sort', ['', 'relevance']);
        $this->Form->setData(['sort' => '']);
        $this->assertEquals('relevance', $this->Form->getData()['sort']);
    }

    public function testGenerateRandomSeedIfNeeded_unneeded() {
        $this->Form->setData(['sort' => 'created']);
        $this->assertFalse($this->Form->generateRandomSeedIfNeeded());
        $this->assertEmpty($this->Form->getData()['rand_seed']);
    }

    public function testGenerateRandomSeedIfNeeded_needed() {
        mt_srand(42);
        $this->Form->setData(['sort' => 'random']);
        $this->assertTrue($this->Form->generateRandomSeedIfNeeded());
        $this->assertEquals('Ztzh', $this->Form->getData()['rand_seed']);
    }

    public function testGetSearchableLists_asGuest() {
        $searcher = null;
        $expected = [
            ['id' => 5, 'user_id' => 1, 'name' => 'Collaborative list'],
            ['id' => 6, 'user_id' => 7, 'name' => 'Inactive list'],
            ['id' => 2, 'user_id' => 7, 'name' => 'Public list'],
        ];
        $this->Form->setData(['list' => '']);
        $result = $this->Form->getSearchableLists($searcher);

        $this->assertEquals($expected, $result->enableHydration(false)->toArray());
    }

    public function testGetSearchableLists_asGuest_withUnlisted() {
        $searcher = null;
        $expected = [
            ['id' => 5, 'user_id' => 1, 'name' => 'Collaborative list'],
            ['id' => 6, 'user_id' => 7, 'name' => 'Inactive list'],
            ['id' => 2, 'user_id' => 7, 'name' => 'Public list'],
            ['id' => 1, 'user_id' => 7, 'name' => 'Interesting French sentences'],
        ];
        $this->Form->setData(['list' => '1']);
        $result = $this->Form->getSearchableLists($searcher);

        $this->assertEquals($expected, $result->enableHydration(false)->toArray());
    }

    public function testGetSearchableLists_asUser() {
        $searcher = 7;
        $expected = [
            ['id' => 5, 'user_id' => 1, 'name' => 'Collaborative list'],
            ['id' => 6, 'user_id' => 7, 'name' => 'Inactive list'],
            ['id' => 1, 'user_id' => 7, 'name' => 'Interesting French sentences'],
            ['id' => 3, 'user_id' => 7, 'name' => 'Private list'],
            ['id' => 2, 'user_id' => 7, 'name' => 'Public list'],
        ];
        $this->Form->setData(['list' => '']);
        $result = $this->Form->getSearchableLists($searcher);

        $this->assertEquals($expected, $result->enableHydration(false)->toArray());
    }

    public function testGetSearchableLists_asUser_withUnlisted() {
        $searcher = 3;
        $expected = [
            ['id' => 5, 'user_id' => 1, 'name' => 'Collaborative list'],
            ['id' => 6, 'user_id' => 7, 'name' => 'Inactive list'],
            ['id' => 2, 'user_id' => 7, 'name' => 'Public list'],
            ['id' => 1, 'user_id' => 7, 'name' => 'Interesting French sentences'],
        ];
        $this->Form->setData(['list' => '1']);
        $result = $this->Form->getSearchableLists($searcher);

        $this->assertEquals($expected, $result->enableHydration(false)->toArray());
    }

    public function testCheckUnwantedCombinations_nop() {
        $this->Form->setData([]);
        $this->Form->checkUnwantedCombinations();
        $this->assertCount(0, $this->Form->getIgnoredFields());
    }

    public function testCheckUnwantedCombinations_orphanWithUser() {
        $this->Form->setData(['user' => 'contributor', 'orphans' => 'yes']);
        $this->Form->checkUnwantedCombinations();

        $this->assertNull($this->Search->getFilter(OrphanFilter::class), 'orphan filter is set');
        $this->assertCount(1, $this->Form->getIgnoredFields());
        $this->assertEquals('', $this->Form->getData()['orphans']);
    }

    public function testCheckUnwantedCombinations_transOrphanWithTransUser() {
        $this->assertMethodCalledWith(
            $this->Search,
            'filterByTranslationOrphanship',
            [true, null]
        );

        $this->Form->setData(['trans_user' => 'contributor', 'trans_orphan' => 'yes']);
        $this->Form->checkUnwantedCombinations();

        $this->assertCount(1, $this->Form->getIgnoredFields());
        $this->assertEquals('', $this->Form->getData()['trans_orphan']);
    }

    public function testCheckUnwantedCombinations_nativeWithoutLanguage() {
        $this->assertMethodCalledWith(
            $this->Search,
            'filterByNativeSpeaker',
            [true, null]
        );

        $this->Form->setData(['from' => '', 'native' => 'yes']);
        $this->Form->checkUnwantedCombinations();

        $this->assertCount(1, $this->Form->getIgnoredFields());
        $this->assertEquals('', $this->Form->getData()['native']);
    }

    public function testCheckUnwantedCombinations_userNotNative() {
        $this->assertMethodCalledWith(
            $this->Search,
            'filterByNativeSpeaker',
            [true, null]
        );

        $this->Form->setData([
            'from' => 'eng',
            'user' => 'contributor',
            'native' => 'yes',
        ]);
        $this->Form->checkUnwantedCombinations();

        $this->assertCount(1, $this->Form->getIgnoredFields());
        $this->assertEquals('', $this->Form->getData()['native']);
    }

    public function testAsSphinx() {
        $search = $this->createMock(\App\Model\Search::class);
        $this->Form->setSearch($search);
        $stuff = [ 'sphinx result' ];
        $search->expects($this->once())
               ->method('asSphinx')
               ->willReturn($stuff);

        $result = $this->Form->asSphinx();

        $this->assertEquals($stuff, $result);
    }

    private function assertSameKeyOrder(array $expected, array $tested) {
        reset($expected);
        reset($tested);
        while (!is_null(key($expected))) {
            $this->assertEquals(key($expected), key($tested));
            next($tested);
            next($expected);
        }
    }

    public function testDataOrderIsPreserved() {
        $expected = [
            'native' => 'yes',
            'user' => '',
            'from' => '',
            'orphans' => 'no',
            'tags' => '',
            'query' => 'order should be preserved',
            'unapproved' => 'no',
            'has_audio' => '',
            'to' => '',
            'list' => '',
        ];
        $this->Form->setData($expected);
        $retreived = $this->Form->getData();
        $this->assertSameKeyOrder($expected, $retreived);
    }
}
