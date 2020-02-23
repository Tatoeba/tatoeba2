<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * LanguagesFixture
 */
class LanguagesFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 3, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'code' => ['type' => 'string', 'length' => 4, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'sentences' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'audio' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'group_1' => ['type' => 'integer', 'length' => 2, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'group_2' => ['type' => 'integer', 'length' => 3, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'group_3' => ['type' => 'integer', 'length' => 4, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'group_4' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'level_0' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'level_1' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'level_2' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'level_3' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'level_4' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'level_5' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'level_unknown' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'lang' => ['type' => 'unique', 'columns' => ['code'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'MyISAM',
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
                'id' => 1,
                'code' => 'ara',
                'sentences' => 1,
                'audio' => 0,
                'group_1' => 0,
                'group_2' => 0,
                'group_3' => 0,
                'group_4' => 0,
                'level_0' => 0,
                'level_1' => 0,
                'level_2' => 0,
                'level_3' => 0,
                'level_4' => 0,
                'level_5' => 0,
                'level_unknown' => 0
            ],
            [
                'id' => 2,
                'code' => 'eng',
                'sentences' => 21,
                'audio' => 0,
                'group_1' => 0,
                'group_2' => 0,
                'group_3' => 0,
                'group_4' => 0,
                'level_0' => 0,
                'level_1' => 0,
                'level_2' => 0,
                'level_3' => 0,
                'level_4' => 0,
                'level_5' => 0,
                'level_unknown' => 0
            ],
            [
                'id' => 3,
                'code' => 'jpn',
                'sentences' => 5,
                'audio' => 0,
                'group_1' => 0,
                'group_2' => 0,
                'group_3' => 0,
                'group_4' => 0,
                'level_0' => 0,
                'level_1' => 1,
                'level_2' => 0,
                'level_3' => 0,
                'level_4' => 0,
                'level_5' => 0,
                'level_unknown' => 0
            ],
            [
                'id' => 4,
                'code' => 'fra',
                'sentences' => 13,
                'audio' => 2,
                'group_1' => 0,
                'group_2' => 0,
                'group_3' => 0,
                'group_4' => 0,
                'level_0' => 0,
                'level_1' => 0,
                'level_2' => 0,
                'level_3' => 0,
                'level_4' => 0,
                'level_5' => 0,
                'level_unknown' => 0
            ],
            [
                'id' => 5,
                'code' => 'deu',
                'sentences' => 1,
                'audio' => 0,
                'group_1' => 0,
                'group_2' => 0,
                'group_3' => 0,
                'group_4' => 0,
                'level_0' => 0,
                'level_1' => 0,
                'level_2' => 0,
                'level_3' => 0,
                'level_4' => 0,
                'level_5' => 0,
                'level_unknown' => 0
            ],
            [
                'id' => 6,
                'code' => 'spa',
                'sentences' => 2,
                'audio' => 1,
                'group_1' => 0,
                'group_2' => 0,
                'group_3' => 0,
                'group_4' => 0,
                'level_0' => 0,
                'level_1' => 0,
                'level_2' => 0,
                'level_3' => 0,
                'level_4' => 0,
                'level_5' => 0,
                'level_unknown' => 0
            ],
            [
                'id' => 7,
                'code' => 'rus',
                'sentences' => 1,
                'audio' => 0,
                'group_1' => 0,
                'group_2' => 0,
                'group_3' => 0,
                'group_4' => 0,
                'level_0' => 0,
                'level_1' => 0,
                'level_2' => 0,
                'level_3' => 0,
                'level_4' => 0,
                'level_5' => 0,
                'level_unknown' => 0
            ],
            [
                'id' => 8,
                'code' => 'cmn',
                'sentences' => 3,
                'audio' => 0,
                'group_1' => 0,
                'group_2' => 0,
                'group_3' => 0,
                'group_4' => 0,
                'level_0' => 0,
                'level_1' => 0,
                'level_2' => 0,
                'level_3' => 0,
                'level_4' => 0,
                'level_5' => 0,
                'level_unknown' => 0
            ],
            [
                'id' => 9,
                'code' => 'ukr',
                'sentences' => 1,
                'audio' => 0,
                'group_1' => 0,
                'group_2' => 0,
                'group_3' => 0,
                'group_4' => 0,
                'level_0' => 0,
                'level_1' => 0,
                'level_2' => 0,
                'level_3' => 0,
                'level_4' => 0,
                'level_5' => 0,
                'level_unknown' => 0
            ],
            [
                'id' => 10,
                'code' => 'tur',
                'sentences' => 2,
                'audio' => 0,
                'group_1' => 0,
                'group_2' => 0,
                'group_3' => 0,
                'group_4' => 0,
                'level_0' => 0,
                'level_1' => 0,
                'level_2' => 0,
                'level_3' => 0,
                'level_4' => 0,
                'level_5' => 0,
                'level_unknown' => 0
            ],
            [
                'id' => 11,
                'code' => 'pol',
                'sentences' => 3,
                'audio' => 0,
                'group_1' => 0,
                'group_2' => 0,
                'group_3' => 0,
                'group_4' => 0,
                'level_0' => 0,
                'level_1' => 0,
                'level_2' => 0,
                'level_3' => 0,
                'level_4' => 0,
                'level_5' => 0,
                'level_unknown' => 0
            ],
            [
                'id' => 12,
                'code' => 'wuu',
                'sentences' => 1,
                'audio' => 0,
                'group_1' => 0,
                'group_2' => 0,
                'group_3' => 0,
                'group_4' => 0,
                'level_0' => 0,
                'level_1' => 0,
                'level_2' => 0,
                'level_3' => 0,
                'level_4' => 0,
                'level_5' => 0,
                'level_unknown' => 0
            ],
            [
                'id' => 13,
                'code' => 'yue',
                'sentences' => 1,
                'audio' => 0,
                'group_1' => 0,
                'group_2' => 0,
                'group_3' => 0,
                'group_4' => 0,
                'level_0' => 0,
                'level_1' => 0,
                'level_2' => 0,
                'level_3' => 0,
                'level_4' => 0,
                'level_5' => 0,
                'level_unknown' => 0
            ],
            [
                'id' => 14,
                'code' => null,
                'sentences' => 1,
                'audio' => 0,
                'group_1' => 0,
                'group_2' => 0,
                'group_3' => 0,
                'group_4' => 0,
                'level_0' => 0,
                'level_1' => 0,
                'level_2' => 0,
                'level_3' => 0,
                'level_4' => 0,
                'level_5' => 0,
                'level_unknown' => 0
            ],
        ];
        parent::init();
    }
}
