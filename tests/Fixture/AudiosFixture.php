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
        'source' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'tatoeba', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
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

    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'sentence_id' => 3,
                'sentence_lang' => 'spa',
                'user_id' => 4,
                'external' => NULL,
                'source' => 'tatoeba',
                'created' => '2014-01-20 09:23:49',
                'modified' => '2014-01-21 21:01:21'
            ],
            [
                'id' => 2,
                'sentence_id' => 4,
                'sentence_lang' => 'fra',
                'user_id' => NULL,
                'external' => [ 'username' => 'Philippe Petit' ],
                'source' => 'tatoeba',
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
                'source' => 'tatoeba',
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
                'source' => 'tatoeba',
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
                'source' => 'tatoeba',
                'created' => '2023-02-01 02:23:33',
                'modified' => '2023-02-01 02:23:33'
            ],
            [
                'id' => 6,
                'sentence_id' => 15,
                'sentence_lang' => 'eng',
                'user_id' => 3,
                'external' => NULL,
                'source' => 'tatoeba',
                'created' => '2023-02-02 02:23:33',
                'modified' => '2023-02-02 02:23:33'
            ],
            [
                'id' => 7,
                'sentence_id' => 57,
                'sentence_lang' => 'jpn',
                'user_id' => 7,
                'external' => NULL,
                'source' => 'tatoeba',
                'created' => '2023-03-03 13:42:42',
                'modified' => '2023-03-03 13:42:42'
            ],
            [
                'id' => 8,
                'sentence_id' => 66,
                'sentence_lang' => 'uzb',
                'user_id' => NULL,
                'external' => [
                    'username' => 'External Contributor',
                    'attribution_url' => 'https://commons.wikimedia.example.org/wiki/File:LL-Q9264_%28uzb%29-Contributor-%D0%98%D1%88%D0%B8%D0%BD%D0%B3%D0%BD%D0%B8%20%D2%9B%D0%B8%D0%BB%21.wav',
                    'license' => 'CC BY 4.0',
                    'download_url' => 'https://upload.wikimedia.example.org/wikipedia/commons/the-file.mp3',
                ],
                'source' => 'commons',
                'created' => '2026-02-03 21:00:01',
                'modified' => '2026-02-03 21:00:01'
            ],
        ];
        parent::init();
    }
}
