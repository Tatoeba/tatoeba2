<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersVocabularyFixture
 *
 */
class UsersVocabularyFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'users_vocabulary';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'user_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'hash' => ['type' => 'binaryuuid', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'vocabulary_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'user_vocabulary' => ['type' => 'unique', 'columns' => ['user_id', 'vocabulary_id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
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
                'user_id' => 4,
                'hash' => '',
                'created' => '2019-01-07 19:50:36',
                'vocabulary_id' => 1
            ],
            [
                'id' => 2,
                'user_id' => 7,
                'hash' => '',
                'created' => '2020-02-20 02:20:02',
                'vocabulary_id' => 2
            ],
            [
                'id' => 3,
                'user_id' => 4,
                'hash' => '',
                'created' => '2020-09-10 11:12:13',
                'vocabulary_id' => 2
            ],
        ];
        parent::init();
    }
}
