<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ContributionsStatsFixture
 *
 */
class ContributionsStatsFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'date' => ['type' => 'date', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'lang' => ['type' => 'string', 'length' => 4, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'sentences' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'action' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'type' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 3061,
                'date' => '2016-11-01 00:00:00',
                'lang' => null,
                'sentences' => 2614,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3062,
                'date' => '2016-11-02 00:00:00',
                'lang' => null,
                'sentences' => 2152,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3063,
                'date' => '2016-11-03 00:00:00',
                'lang' => null,
                'sentences' => 3465,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3064,
                'date' => '2016-11-04 00:00:00',
                'lang' => null,
                'sentences' => 2865,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3065,
                'date' => '2016-11-05 00:00:00',
                'lang' => null,
                'sentences' => 3895,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3066,
                'date' => '2016-11-06 00:00:00',
                'lang' => null,
                'sentences' => 3148,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3067,
                'date' => '2016-11-07 00:00:00',
                'lang' => null,
                'sentences' => 2389,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3068,
                'date' => '2016-11-08 00:00:00',
                'lang' => null,
                'sentences' => 2205,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3069,
                'date' => '2016-11-09 00:00:00',
                'lang' => null,
                'sentences' => 3175,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3070,
                'date' => '2016-11-10 00:00:00',
                'lang' => null,
                'sentences' => 2918,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3071,
                'date' => '2016-11-11 00:00:00',
                'lang' => null,
                'sentences' => 2716,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3072,
                'date' => '2016-11-12 00:00:00',
                'lang' => null,
                'sentences' => 3406,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3073,
                'date' => '2016-11-13 00:00:00',
                'lang' => null,
                'sentences' => 4061,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3074,
                'date' => '2016-11-14 00:00:00',
                'lang' => null,
                'sentences' => 2927,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3075,
                'date' => '2016-11-15 00:00:00',
                'lang' => null,
                'sentences' => 3219,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3076,
                'date' => '2016-11-16 00:00:00',
                'lang' => null,
                'sentences' => 2314,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3077,
                'date' => '2016-11-17 00:00:00',
                'lang' => null,
                'sentences' => 3208,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3078,
                'date' => '2016-11-18 00:00:00',
                'lang' => null,
                'sentences' => 2917,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3079,
                'date' => '2016-11-19 00:00:00',
                'lang' => null,
                'sentences' => 2262,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3080,
                'date' => '2016-11-20 00:00:00',
                'lang' => null,
                'sentences' => 2509,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3081,
                'date' => '2016-11-21 00:00:00',
                'lang' => null,
                'sentences' => 2772,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3082,
                'date' => '2016-11-22 00:00:00',
                'lang' => null,
                'sentences' => 2424,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3083,
                'date' => '2016-11-23 00:00:00',
                'lang' => null,
                'sentences' => 2286,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3084,
                'date' => '2016-11-24 00:00:00',
                'lang' => null,
                'sentences' => 2416,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3085,
                'date' => '2016-11-25 00:00:00',
                'lang' => null,
                'sentences' => 1936,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3086,
                'date' => '2016-11-26 00:00:00',
                'lang' => null,
                'sentences' => 2002,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3087,
                'date' => '2016-11-27 00:00:00',
                'lang' => null,
                'sentences' => 2684,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3088,
                'date' => '2016-11-28 00:00:00',
                'lang' => null,
                'sentences' => 2171,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3089,
                'date' => '2016-11-29 00:00:00',
                'lang' => null,
                'sentences' => 1823,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3090,
                'date' => '2016-11-30 00:00:00',
                'lang' => null,
                'sentences' => 2201,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3091,
                'date' => '2016-12-01 00:00:00',
                'lang' => null,
                'sentences' => 2324,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3092,
                'date' => '2016-12-02 00:00:00',
                'lang' => null,
                'sentences' => 2778,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3093,
                'date' => '2016-12-03 00:00:00',
                'lang' => null,
                'sentences' => 1894,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3094,
                'date' => '2016-12-04 00:00:00',
                'lang' => null,
                'sentences' => 2876,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3095,
                'date' => '2016-12-05 00:00:00',
                'lang' => null,
                'sentences' => 2573,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3096,
                'date' => '2016-12-06 00:00:00',
                'lang' => null,
                'sentences' => 2896,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3097,
                'date' => '2016-12-07 00:00:00',
                'lang' => null,
                'sentences' => 3315,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3098,
                'date' => '2016-12-08 00:00:00',
                'lang' => null,
                'sentences' => 2628,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3099,
                'date' => '2016-12-09 00:00:00',
                'lang' => null,
                'sentences' => 2482,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3100,
                'date' => '2016-12-10 00:00:00',
                'lang' => null,
                'sentences' => 2361,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3101,
                'date' => '2016-12-11 00:00:00',
                'lang' => null,
                'sentences' => 2067,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3102,
                'date' => '2016-12-12 00:00:00',
                'lang' => null,
                'sentences' => 3106,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3103,
                'date' => '2016-12-13 00:00:00',
                'lang' => null,
                'sentences' => 2721,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3104,
                'date' => '2016-12-14 00:00:00',
                'lang' => null,
                'sentences' => 2343,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3105,
                'date' => '2016-12-15 00:00:00',
                'lang' => null,
                'sentences' => 2371,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3106,
                'date' => '2016-12-16 00:00:00',
                'lang' => null,
                'sentences' => 2175,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3107,
                'date' => '2016-12-17 00:00:00',
                'lang' => null,
                'sentences' => 1655,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3108,
                'date' => '2016-12-18 00:00:00',
                'lang' => null,
                'sentences' => 2090,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3109,
                'date' => '2016-12-19 00:00:00',
                'lang' => null,
                'sentences' => 2256,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3110,
                'date' => '2016-12-20 00:00:00',
                'lang' => null,
                'sentences' => 1649,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3111,
                'date' => '2016-12-21 00:00:00',
                'lang' => null,
                'sentences' => 2052,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3112,
                'date' => '2016-12-22 00:00:00',
                'lang' => null,
                'sentences' => 2032,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3113,
                'date' => '2016-12-23 00:00:00',
                'lang' => null,
                'sentences' => 2202,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3114,
                'date' => '2016-12-24 00:00:00',
                'lang' => null,
                'sentences' => 1651,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3115,
                'date' => '2016-12-25 00:00:00',
                'lang' => null,
                'sentences' => 1348,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3116,
                'date' => '2016-12-26 00:00:00',
                'lang' => null,
                'sentences' => 2738,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3117,
                'date' => '2016-12-27 00:00:00',
                'lang' => null,
                'sentences' => 2897,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3118,
                'date' => '2016-12-28 00:00:00',
                'lang' => null,
                'sentences' => 2273,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3119,
                'date' => '2016-12-29 00:00:00',
                'lang' => null,
                'sentences' => 3055,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3120,
                'date' => '2016-12-30 00:00:00',
                'lang' => null,
                'sentences' => 2680,
                'action' => 'insert',
                'type' => 'sentence'
            ],
            [
                'id' => 3121,
                'date' => '2016-11-01 00:00:00',
                'lang' => null,
                'sentences' => 5000,
                'action' => 'insert',
                'type' => 'link'
            ],
            [
                'id' => 3122,
                'date' => '2016-11-01 00:00:00',
                'lang' => null,
                'sentences' => 200,
                'action' => 'delete',
                'type' => 'link'
            ],
            [
                'id' => 3123,
                'date' => '2016-11-01 00:00:00',
                'lang' => null,
                'sentences' => 15,
                'action' => 'delete',
                'type' => 'sentence'
            ],
            [
                'id' => 3124,
                'date' => '2016-11-30 00:00:00',
                'lang' => null,
                'sentences' => 3000,
                'action' => 'insert',
                'type' => 'link'
            ]
        ];
        parent::init();
    }
}
