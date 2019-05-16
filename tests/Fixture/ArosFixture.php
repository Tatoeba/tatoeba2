<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ArosFixture
 *
 */
class ArosFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'parent_id' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'model' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'foreign_key' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'alias' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'lft' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'rght' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'idx_aros_model_foreign_key' => ['type' => 'index', 'columns' => ['model', 'foreign_key'], 'length' => []],
            'idx_aros_lft_rght' => ['type' => 'index', 'columns' => ['lft', 'rght'], 'length' => []],
            'idx_aros_alias' => ['type' => 'index', 'columns' => ['alias'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'latin1_swedish_ci'
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
                'parent_id' => null,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'Groups',
                'lft' => 1,
                'rght' => 32
            ],
            [
                'id' => 2,
                'parent_id' => 1,
                'model' => 'Groups',
                'foreign_key' => 1,
                'alias' => null,
                'lft' => 2,
                'rght' => 5
            ],
            [
                'id' => 3,
                'parent_id' => 1,
                'model' => 'Groups',
                'foreign_key' => 2,
                'alias' => null,
                'lft' => 6,
                'rght' => 9
            ],
            [
                'id' => 4,
                'parent_id' => 1,
                'model' => 'Groups',
                'foreign_key' => 3,
                'alias' => null,
                'lft' => 10,
                'rght' => 13
            ],
            [
                'id' => 5,
                'parent_id' => 1,
                'model' => 'Groups',
                'foreign_key' => 4,
                'alias' => null,
                'lft' => 14,
                'rght' => 23
            ],
            [
                'id' => 6,
                'parent_id' => 1,
                'model' => 'Groups',
                'foreign_key' => 5,
                'alias' => null,
                'lft' => 24,
                'rght' => 27
            ],
            [
                'id' => 7,
                'parent_id' => 1,
                'model' => 'Groups',
                'foreign_key' => 6,
                'alias' => null,
                'lft' => 28,
                'rght' => 31
            ],
            [
                'id' => 8,
                'parent_id' => 2,
                'model' => 'Users',
                'foreign_key' => 1,
                'alias' => null,
                'lft' => 3,
                'rght' => 4
            ],
            [
                'id' => 9,
                'parent_id' => 3,
                'model' => 'Users',
                'foreign_key' => 2,
                'alias' => null,
                'lft' => 7,
                'rght' => 8
            ],
            [
                'id' => 10,
                'parent_id' => 4,
                'model' => 'Users',
                'foreign_key' => 3,
                'alias' => null,
                'lft' => 11,
                'rght' => 12
            ],
            [
                'id' => 11,
                'parent_id' => 5,
                'model' => 'Users',
                'foreign_key' => 4,
                'alias' => null,
                'lft' => 15,
                'rght' => 16
            ],
            [
                'id' => 12,
                'parent_id' => 6,
                'model' => 'Users',
                'foreign_key' => 5,
                'alias' => null,
                'lft' => 25,
                'rght' => 26
            ],
            [
                'id' => 13,
                'parent_id' => 7,
                'model' => 'Users',
                'foreign_key' => 6,
                'alias' => null,
                'lft' => 29,
                'rght' => 30
            ],
            [
                'id' => 14,
                'parent_id' => 5,
                'model' => 'Users',
                'foreign_key' => 7,
                'alias' => null,
                'lft' => 17,
                'rght' => 18
            ],
            [
                'id' => 15,
                'parent_id' => 5,
                'model' => 'Users',
                'foreign_key' => 8,
                'alias' => null,
                'lft' => 19,
                'rght' => 20
            ],
            [
                'id' => 16,
                'parent_id' => 5,
                'model' => 'Users',
                'foreign_key' => 9,
                'alias' => null,
                'lft' => 21,
                'rght' => 22
            ],
        ];
        parent::init();
    }
}
