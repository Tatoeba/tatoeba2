<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ArosAcosFixture
 *
 */
class ArosAcosFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'aro_id' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'aco_id' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_create' => ['type' => 'string', 'length' => 2, 'null' => false, 'default' => '0', 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        '_read' => ['type' => 'string', 'length' => 2, 'null' => false, 'default' => '0', 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        '_update' => ['type' => 'string', 'length' => 2, 'null' => false, 'default' => '0', 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        '_delete' => ['type' => 'string', 'length' => 2, 'null' => false, 'default' => '0', 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        '_indexes' => [
            'aco_id' => ['type' => 'index', 'columns' => ['aco_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'ARO_ACO_KEY' => ['type' => 'unique', 'columns' => ['aro_id', 'aco_id'], 'length' => []],
            'idx_aros_acos_aro_id_aco_id' => ['type' => 'unique', 'columns' => ['aro_id', 'aco_id'], 'length' => []],
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
                'aro_id' => 2,
                'aco_id' => 1,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 2,
                'aro_id' => 3,
                'aco_id' => 2,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 3,
                'aro_id' => 3,
                'aco_id' => 8,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 4,
                'aro_id' => 3,
                'aco_id' => 11,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 5,
                'aro_id' => 3,
                'aco_id' => 21,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 6,
                'aro_id' => 3,
                'aco_id' => 28,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 7,
                'aro_id' => 3,
                'aco_id' => 32,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 8,
                'aro_id' => 3,
                'aco_id' => 33,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 9,
                'aro_id' => 3,
                'aco_id' => 34,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 10,
                'aro_id' => 3,
                'aco_id' => 44,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 11,
                'aro_id' => 3,
                'aco_id' => 45,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 12,
                'aro_id' => 3,
                'aco_id' => 47,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 13,
                'aro_id' => 3,
                'aco_id' => 57,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 14,
                'aro_id' => 3,
                'aco_id' => 62,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 15,
                'aro_id' => 3,
                'aco_id' => 72,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 16,
                'aro_id' => 3,
                'aco_id' => 73,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 17,
                'aro_id' => 3,
                'aco_id' => 74,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 18,
                'aro_id' => 3,
                'aco_id' => 75,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 19,
                'aro_id' => 3,
                'aco_id' => 76,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 20,
                'aro_id' => 3,
                'aco_id' => 79,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 21,
                'aro_id' => 3,
                'aco_id' => 82,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 22,
                'aro_id' => 3,
                'aco_id' => 87,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 23,
                'aro_id' => 3,
                'aco_id' => 88,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 24,
                'aro_id' => 3,
                'aco_id' => 89,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 25,
                'aro_id' => 3,
                'aco_id' => 93,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 26,
                'aro_id' => 3,
                'aco_id' => 94,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 27,
                'aro_id' => 3,
                'aco_id' => 96,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 28,
                'aro_id' => 3,
                'aco_id' => 106,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 29,
                'aro_id' => 3,
                'aco_id' => 102,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 30,
                'aro_id' => 4,
                'aco_id' => 2,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 31,
                'aro_id' => 4,
                'aco_id' => 8,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 32,
                'aro_id' => 4,
                'aco_id' => 11,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 33,
                'aro_id' => 4,
                'aco_id' => 21,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 34,
                'aro_id' => 4,
                'aco_id' => 28,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 35,
                'aro_id' => 4,
                'aco_id' => 32,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 36,
                'aro_id' => 4,
                'aco_id' => 33,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 37,
                'aro_id' => 4,
                'aco_id' => 34,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 38,
                'aro_id' => 4,
                'aco_id' => 44,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 39,
                'aro_id' => 4,
                'aco_id' => 45,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 40,
                'aro_id' => 4,
                'aco_id' => 47,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 41,
                'aro_id' => 4,
                'aco_id' => 57,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 42,
                'aro_id' => 4,
                'aco_id' => 62,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 43,
                'aro_id' => 4,
                'aco_id' => 72,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 44,
                'aro_id' => 4,
                'aco_id' => 73,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 45,
                'aro_id' => 4,
                'aco_id' => 74,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 46,
                'aro_id' => 4,
                'aco_id' => 75,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 47,
                'aro_id' => 4,
                'aco_id' => 76,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 48,
                'aro_id' => 4,
                'aco_id' => 79,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 49,
                'aro_id' => 4,
                'aco_id' => 82,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 50,
                'aro_id' => 4,
                'aco_id' => 87,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 51,
                'aro_id' => 4,
                'aco_id' => 88,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 52,
                'aro_id' => 4,
                'aco_id' => 89,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 53,
                'aro_id' => 4,
                'aco_id' => 93,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 54,
                'aro_id' => 4,
                'aco_id' => 94,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 55,
                'aro_id' => 4,
                'aco_id' => 96,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 56,
                'aro_id' => 4,
                'aco_id' => 106,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 57,
                'aro_id' => 4,
                'aco_id' => 102,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 58,
                'aro_id' => 5,
                'aco_id' => 2,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 59,
                'aro_id' => 5,
                'aco_id' => 11,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 60,
                'aro_id' => 5,
                'aco_id' => 21,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 61,
                'aro_id' => 5,
                'aco_id' => 28,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 62,
                'aro_id' => 5,
                'aco_id' => 32,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 63,
                'aro_id' => 5,
                'aco_id' => 33,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 64,
                'aro_id' => 5,
                'aco_id' => 34,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 65,
                'aro_id' => 5,
                'aco_id' => 44,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 66,
                'aro_id' => 5,
                'aco_id' => 45,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 67,
                'aro_id' => 5,
                'aco_id' => 47,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 68,
                'aro_id' => 5,
                'aco_id' => 62,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 69,
                'aro_id' => 5,
                'aco_id' => 72,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 70,
                'aro_id' => 5,
                'aco_id' => 73,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 71,
                'aro_id' => 5,
                'aco_id' => 74,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 72,
                'aro_id' => 5,
                'aco_id' => 75,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 73,
                'aro_id' => 5,
                'aco_id' => 76,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 74,
                'aro_id' => 5,
                'aco_id' => 79,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 75,
                'aro_id' => 5,
                'aco_id' => 82,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 76,
                'aro_id' => 5,
                'aco_id' => 87,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 77,
                'aro_id' => 5,
                'aco_id' => 88,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 78,
                'aro_id' => 5,
                'aco_id' => 89,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 79,
                'aro_id' => 5,
                'aco_id' => 93,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 80,
                'aro_id' => 5,
                'aco_id' => 94,
                '_create' => '-1',
                '_read' => '-1',
                '_update' => '-1',
                '_delete' => '-1'
            ],
            [
                'id' => 81,
                'aro_id' => 5,
                'aco_id' => 96,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 82,
                'aro_id' => 5,
                'aco_id' => 106,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
            [
                'id' => 83,
                'aro_id' => 5,
                'aco_id' => 102,
                '_create' => '1',
                '_read' => '1',
                '_update' => '1',
                '_delete' => '1'
            ],
        ];
        parent::init();
    }
}
