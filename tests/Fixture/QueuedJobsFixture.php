<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class QueuedJobsFixture extends TestFixture
{
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'job_type' => ['type' => 'string', 'length' => 45, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'data' => ['type' => 'text', 'length' => 16777215, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'job_group' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'reference' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'notbefore' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'fetched' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'completed' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'progress' => ['type' => 'float', 'length' => null, 'precision' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => ''],
        'failed' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'failure_message' => ['type' => 'text', 'length' => 16777215, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'workerkey' => ['type' => 'string', 'length' => 45, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'status' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'priority' => ['type' => 'integer', 'length' => 3, 'unsigned' => false, 'null' => false, 'default' => '5', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
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
