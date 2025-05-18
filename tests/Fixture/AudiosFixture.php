<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AudiosFixture
 */
class AudiosFixture extends TestFixture
{
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'sentence_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'sentence_lang' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
        'user_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'external' => ['type' => 'json', 'length' => 500, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'sentence_id' => ['type' => 'index', 'columns' => ['sentence_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];

    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'sentence_id' => 3,
                'sentence_lang' => 'spa',
                'user_id' => 4,
                'external' => NULL,
                'created' => '2014-01-20 09:23:49',
                'modified' => '2014-01-21 21:01:21'
            ],
            [
                'id' => 2,
                'sentence_id' => 4,
                'sentence_lang' => 'fra',
                'user_id' => NULL,
                'external' => [ 'username' => 'Philippe Petit' ],
                'created' => '2001-12-02 06:47:30',
                'modified' => '2001-12-12 06:47:30'
            ],
            [
                'id' => 3,
                'sentence_id' => 12,
                'sentence_lang' => 'fra',
                'user_id' => NULL,
                'external' => [
                    'username' => 'Philippe Petit',
                    'attribution_url' => 'https://example.fr/petit',
                    'license' => 'CC BY-NC 4.0',
                ],
                'created' => '2001-12-02 06:47:30',
                'modified' => '2001-12-12 06:47:30'
            ],
            /**
             * Do not include an audio with id=4 here because it
             * already exists in the DisabledAudios fixture
             *
            [
                'id' => 4,
                'sentence_id' => 3,
                'sentence_lang' => 'spa',
                'user_id' => 2,
                'external' => NULL,
                'created' => '2022-01-20 09:23:49',
                'modified' => '2022-01-21 21:01:21'
            ],
            */
            [
                'id' => 5,
                'sentence_id' => 4,
                'sentence_lang' => 'fra',
                'user_id' => 3,
                'external' => NULL,
                'created' => '2023-02-01 02:23:33',
                'modified' => '2023-02-01 02:23:33'
            ],
            [
                'id' => 6,
                'sentence_id' => 15,
                'sentence_lang' => 'eng',
                'user_id' => 3,
                'external' => NULL,
                'created' => '2023-02-02 02:23:33',
                'modified' => '2023-02-02 02:23:33'
            ],
            [
                'id' => 7,
                'sentence_id' => 57,
                'sentence_lang' => 'jpn',
                'user_id' => 7,
                'external' => NULL,
                'created' => '2023-03-03 13:42:42',
                'modified' => '2023-03-03 13:42:42'
            ],
        ];
        parent::init();
    }
}
