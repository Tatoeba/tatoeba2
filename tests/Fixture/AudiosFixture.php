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
        'audio_idx' => ['type' => 'integer', 'length' => 4, 'unsigned' => false, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'user_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'external' => ['type' => 'json', 'length' => 500, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'sentence_id_audio_idx' => ['type' => 'unique', 'columns' => ['sentence_id', 'audio_idx'], 'length' => []],
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
                'audio_idx' => 1,
                'user_id' => 4,
                'external' => NULL,
                'created' => '2014-01-20 09:23:49',
                'modified' => '2014-01-21 21:01:21'
            ],
            [
                'id' => 2,
                'sentence_id' => 4,
                'audio_idx' => 1,
                'user_id' => NULL,
                'external' => [ 'username' => 'Philippe Petit' ],
                'created' => '2001-12-02 06:47:30',
                'modified' => '2001-12-12 06:47:30'
            ],
            [
                'id' => 3,
                'sentence_id' => 12,
                'audio_idx' => 1,
                'user_id' => NULL,
                'external' => [ 'username' => 'Philippe Petit' ],
                'created' => '2001-12-02 06:47:30',
                'modified' => '2001-12-12 06:47:30'
            ],
        ];
        parent::init();
    }
}
