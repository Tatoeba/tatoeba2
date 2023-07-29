<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersLanguagesFixture
 */
class UsersLanguagesFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'of_user_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'by_user_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'language_code' => ['type' => 'string', 'length' => 4, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'level' => ['type' => 'tinyinteger', 'length' => 2, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => '', 'precision' => null],
        'level_approval_status' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'pending', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'details' => ['type' => 'text', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'user_lang' => ['type' => 'unique', 'columns' => ['of_user_id', 'by_user_id', 'language_code'], 'length' => []],
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
                'of_user_id' => 4,
                'by_user_id' => 4,
                'language_code' => 'jpn',
                'level' => 1,
                'details' => '',
                'created' => '2018-10-31 00:00:00',
                'modified' => '2018-10-31 00:00:00'
            ],
            [
                'id' => 2,
                'of_user_id' => 4,
                'by_user_id' => 4,
                'language_code' => 'fra',
                'level' => 5,
                'details' => '',
                'created' => '2018-10-31 00:00:00',
                'modified' => '2018-10-31 00:00:00'
            ],
            [
                'id' => 3,
                'of_user_id' => 7,
                'by_user_id' => 7,
                'language_code' => 'jpn',
                'level' => 5,
                'details' => '',
                'created' => '2018-10-31 00:00:00',
                'modified' => '2018-10-31 00:00:00'
            ],
            [
                'id' => 4,
                'of_user_id' => 3,
                'by_user_id' => 3,
                'language_code' => 'fra',
                'level' => 5,
                'details' => '',
                'created' => '2018-10-31 00:00:00',
                'modified' => '2018-10-31 00:00:00'
            ],
            [
                'id' => 5,
                'of_user_id' => 7,
                'by_user_id' => 7,
                'language_code' => 'fra',
                'level' => 2,
                'details' => '',
                'created' => '2018-10-31 00:00:00',
                'modified' => '2018-10-31 00:00:00'
            ],
            [
                'id' => 6,
                'of_user_id' => 2,
                'by_user_id' => 2,
                'language_code' => 'jpn',
                'level' => 3,
                'details' => '',
                'created' => '2017-10-31 00:00:00',
                'modified' => '2017-10-31 00:00:00'
            ],
        ];
        parent::init();
    }
}
