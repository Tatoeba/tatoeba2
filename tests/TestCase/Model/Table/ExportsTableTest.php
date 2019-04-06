<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ExportsTable;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class ExportsTableTest extends TestCase
{
    public $Exports;

    public $fixtures = [
        'app.Exports',
        'app.QueuedJobs',
        'app.Users',
        'app.Sentences',
    ];

    private $testExportDir = TMP.'export_tests'.DS;

    public function setUp()
    {
        parent::setUp();

        Configure::write('Exports', [
            'path' => $this->testExportDir,
            'url'  => 'https://example.com/exports/',
        ]);
        Configure::write('Acl.database', 'test');

        \Cake\Core\Plugin::load('Queue');

        $folder = new Folder($this->testExportDir);
        $folder->delete();
        if (!$folder->create($this->testExportDir)) {
            die("Couldn't create test directory '{$this->testExportDir}'");
        }

        $this->Exports = TableRegistry::get('Exports');
    }

    public function tearDown()
    {
        unset($this->Exports);

        $folder = new Folder($this->testExportDir);
        $folder->delete();

        parent::tearDown();
    }

    private function options()
    {
        return [ 'type' => 'list', 'name' => 'foo', 'description' => 'bar' ];
    }

    private function optionsWith($with)
    {
        return array_merge($this->options(), $with);
    }

    private function optionsWithout($without)
    {
        $options = $this->options();
        foreach ($without as $field) {
            unset($options[$field]);
        }
        return $options;
    }

    private function assertResultSet($expectedResultSet, $resultSet)
    {
        $i = 0;
        foreach ($resultSet as $entity) {
            $expected = $expectedResultSet[$i];
            $this->assertEquals($expected, $entity->toArray(), "Item $i of result set is not as expected");
            $i++;
        }
    }

    public function testGetExportsOf()
    {
        $expected = [
            [
                'name' => 'Kazuki\'s sentences',
                'status' => 'online',
                'url' => 'https://downloads.tatoeba.org/exports/kazuki_sentences.zip',
            ],
            [
                'name' => 'Japanese-Russian sentence pairs',
                'status' => 'queued',
                'url' => null,
            ],
        ];

        $result = $this->Exports->getExportsOf(7);

        $this->assertResultSet($expected, $result->all());
    }

    public function testCreateExport_returnsExport()
    {
        $expected = [
            'name' => 'foo',
            'url' => null,
            'status' => 'queued',
        ];
        $options = [ 'type' => 'list', 'name' => 'foo', 'description' => 'bar' ];

        $export = $this->Exports->createExport(4, $options);

        $this->assertEquals($expected, $export);
    }

    public function testCreateExport_failsIfEmptyName()
    {
        $options = $this->optionsWith(['name' => '']);
        $result = $this->Exports->createExport(4, $options);
        $this->assertFalse($result);
    }

    public function testCreateExport_failsWithoutName()
    {
        $options = $this->optionsWithout(['name']);
        $result = $this->Exports->createExport(4, $options);
        $this->assertFalse($result);
    }

    public function testCreateExport_failsIfEmptyDescription()
    {
        $options = $this->optionsWith(['description' => '']);
        $result = $this->Exports->createExport(4, $options);
        $this->assertFalse($result);
    }

    public function testCreateExport_failsWithoutDescription()
    {
        $options = $this->optionsWithout(['description']);
        $result = $this->Exports->createExport(4, $options);
        $this->assertFalse($result);
    }

    public function testCreateExport_failsIfInvalidUserId()
    {
        $options = $this->options();
        $result = $this->Exports->createExport(9999999999, $options);
        $this->assertFalse($result);
    }

    public function testCreateExport_failsIfCreatingJobFails()
    {
        $rules = $this->Exports->QueuedJobs->rulesChecker();
        $rules->add(function () {return false;}, 'always-fail');
        $before = $this->Exports->find()->count();

        $result = $this->Exports->createExport(4, $this->options());
        $this->assertFalse($result);

        $after = $this->Exports->find()->count();
        $this->assertEquals($before, $after);
    }

    public function testCreateExport_createsExport()
    {
        $expected = [
            'id' => 4,
            'name' => 'foo',
            'description' => 'bar',
            'url' => null,
            'filename' => null,
            'generated' => null,
            'status' => 'queued',
            'queued_job_id' => 4,
            'user_id' => 4,
        ];
        $options = [ 'type' => 'list', 'name' => 'foo', 'description' => 'bar' ];

        $this->Exports->createExport(4, $options);

        $export = $this->Exports->find()->last()->toArray();
        $this->assertEquals($expected, $export);
    }

    public function testCreateExport_createsJob()
    {
        $options = [ 'type' => 'list', 'name' => 'foo', 'description' => 'bar' ];

        $this->Exports->createExport(4, $options);

        $job = $this->Exports->QueuedJobs->find()->last();
        $this->assertEquals('Export', $job->job_type);
        $this->assertEquals(4, $job->job_group);

        $export = $this->Exports->find()->last();
        $this->assertEquals($export->id, unserialize($job->data)['export_id']);
    }

    public function testRunExport_returnsTrue()
    {
        $jobId = 3;
        $exportId = 3;
        $config = (array)unserialize($this->Exports->QueuedJobs->get($jobId)->data);

        $result = $this->Exports->runExport($config, $jobId);

        $this->assertTrue($result);
    }

    public function testRunExport_updatesExport()
    {
        $now = new Time('2019-02-01 15:04:30');
        Time::setTestNow($now);

        $jobId = 3;
        $exportId = 3;
        $config = (array)unserialize($this->Exports->QueuedJobs->get($jobId)->data);

        $this->Exports->runExport($config, $jobId);

        $export = $this->Exports->get($exportId);
        $this->assertEquals($now, $export->generated);
        $this->assertEquals(TMP.'export_tests/list_3.csv', $export->filename);
        $this->assertEquals('https://example.com/exports/list_3.csv', $export->url);
        $this->assertEquals('online', $export->status);
    }

    public function testRunExport_createsFile()
    {
        $jobId = 3;
        $exportId = 3;
        $config = (array)unserialize($this->Exports->QueuedJobs->get($jobId)->data);

        $this->Exports->runExport($config, $jobId);

        $filename = $this->Exports->get($exportId)->filename;
        $this->assertFileEquals(TESTS . 'Fixture'.DS.'list_3.csv', $filename);
    }

    public function testRunExport_withBatchOperation()
    {
        $this->Exports->batchOperationSize = 1;
        $this->testRunExport_createsFile();
    }

    public function testRunExport_failsIfExportDirNotWritable()
    {
        $readOnlyDir = $this->testExportDir.'readonly';
        $folder = new Folder($readOnlyDir, true, 0444);
        Configure::write('Exports.path', $folder->path.DS);

        $jobId = 3;
        $exportId = 3;
        $config = (array)unserialize($this->Exports->QueuedJobs->get($jobId)->data);

        $result = $this->Exports->runExport($config, $jobId);

        $this->assertFalse($result);
        $this->assertEquals('failed', $this->Exports->get($exportId)->status);
    }

    public function testRunExport_failsIfInvalidExportId()
    {
        $jobId = 3;
        $config = ['type' => 'list', 'list_id' => 1, 'export_id' => 999999999];

        $result = $this->Exports->runExport($config, $jobId);

        $this->assertFalse($result);
    }

    public function testRunExport_failsIfInvalidListId()
    {
        $exportId = 3;
        $jobId = 3;
        $config = ['type' => 'list', 'list_id' => 'invalid', 'export_id' => $exportId];

        $result = $this->Exports->runExport($config, $jobId);

        $this->assertFalse($result);
        $this->assertEquals('failed', $this->Exports->get($exportId)->status);
    }

    public function testRunExport_failsIfInvalidType()
    {
        $exportId = 3;
        $jobId = 3;
        $config = ['type' => 'invalid', 'export_id' => $exportId];

        $result = $this->Exports->runExport($config, $jobId);

        $this->assertFalse($result);
        $this->assertEquals('failed', $this->Exports->get($exportId)->status);
    }

}
