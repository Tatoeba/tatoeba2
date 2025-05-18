<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class ExportsFixture extends TestFixture
{
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'name' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'description' => ['type' => 'text', 'length' => null, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '', 'precision' => null],
        'url' => ['type' => 'string', 'length' => 2048, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'filename' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'generated' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'status' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'queued_job_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'user_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_general_ci'
        ],
    ];

    public function init()
    {
        $this->records = [
            [
                /* Export completed, by kazuki */
                'id' => 1,
                'name' => 'Kazuki\'s sentences',
                'description' => 'Sentence id [tab] Sentence text',
                'url' => '/export_tests/kazuki_sentences.zip',
                'filename' => TMP.'/export_tests/kazuki_sentences.zip',
                'generated' => '2019-02-01 14:54:13',
                'status' => 'online',
                'queued_job_id' => 1,
                'user_id' => 7
            ],
            [
                /* Export in process */
                'id' => 2,
                'name' => 'Japanese-Russian sentence pairs',
                'description' => 'Japanese sentence text [tab] Russian entence text',
                'url' => null,
                'filename' => TMP.'/export_tests/pairs_2.tsv',
                'generated' => '2019-02-01 15:04:02',
                'status' => 'queued',
                'queued_job_id' => 2,
                'user_id' => 7
            ],
            [
                /* Export waiting to be proceeded */
                'id' => 3,
                'name' => 'List 1234',
                'description' => 'Sentence id [tab] Sentence text',
                'url' => null,
                'filename' => null,
                'generated' => null,
                'status' => 'queued',
                'queued_job_id' => 3,
                'user_id' => 4
            ],
            [
                /* Export completed, by guest */
                'id' => 4,
                'name' => 'Kazuki\'s sentences',
                'description' => 'Sentence id [tab] Sentence text',
                'url' => '/export_tests/kazuki_sentences.zip',
                'filename' => TMP.'/export_tests/kazuki_sentences.zip',
                'generated' => '2019-02-20 20:50:13',
                'status' => 'online',
                'queued_job_id' => 4,
                'user_id' => null
            ],
        ];
        parent::init();
    }
}
