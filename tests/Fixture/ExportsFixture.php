<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class ExportsFixture extends TestFixture
{
    public function init(): void
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
