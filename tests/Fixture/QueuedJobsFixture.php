<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class QueuedJobsFixture extends TestFixture
{
    public function init(): void
    {
        $this->records = [
            [
                /* Job completed */
                'id' => 1,
                'job_type' => 'Export',
                'data' => serialize(['param' => 'foo']),
                'job_group' => '7',
                'reference' => null,
                'created' => '2019-02-01 14:54:10',
                'notbefore' => null,
                'fetched' => '2019-02-01 14:54:13',
                'completed' => '2019-02-01 14:54:42',
                'progress' => null,
                'failed' => 0,
                'failure_message' => null,
                'workerkey' => '5cc0b3dcea22457717fe2856ea6beb1329807ba2',
                'status' => null,
                'priority' => 5
            ],
            [
                /* Job being executed */
                'id' => 2,
                'job_type' => 'Export',
                'data' => serialize(['param' => 'foo']),
                'job_group' => '7',
                'reference' => null,
                'created' => '2019-02-01 15:03:55',
                'notbefore' => null,
                'fetched' => '2019-02-01 15:04:02',
                'completed' => null,
                'progress' => null,
                'failed' => 0,
                'failure_message' => null,
                'workerkey' => '5cc0b3dcea22457717fe2856ea6beb1329807ba2',
                'status' => null,
                'priority' => 5
            ],
            [
                /* Job waiting to be executed */
                'id' => 3,
                'job_type' => 'Export',
                'data' => serialize(['type' => 'list', 'list_id' => 1, 'export_id' => 3, 'format' => 'tsv', 'fields' => ['id', 'lang', 'text']]),
                'job_group' => '4',
                'reference' => null,
                'created' => '2019-02-01 15:03:56',
                'notbefore' => null,
                'fetched' => null,
                'completed' => null,
                'progress' => null,
                'failed' => 0,
                'failure_message' => null,
                'workerkey' => null,
                'status' => null,
                'priority' => 5
            ],
            [
                /* Job completed */
                'id' => 4,
                'job_type' => 'Export',
                'data' => serialize(['param' => 'foo']),
                'job_group' => null, // does not belong to any user
                'reference' => null,
                'created' => '2019-02-20 20:50:10',
                'notbefore' => null,
                'fetched' => '2019-02-20 20:50:13',
                'completed' => '2019-02-20 20:50:13',
                'progress' => null,
                'failed' => 0,
                'failure_message' => null,
                'workerkey' => '5cc0b3dcea22457717fe2856ea6beb1329807ba2',
                'status' => null,
                'priority' => 5
            ],
        ];
        parent::init();
    }
}
