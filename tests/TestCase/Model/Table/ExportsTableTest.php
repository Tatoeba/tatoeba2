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
        'app.Links',
        'app.QueuedJobs',
        'app.Users',
        'app.Sentences',
        'app.SentencesLists',
        'app.SentencesSentencesLists',
        'plugin.Queue.QueueProcesses',
    ];

    private $testExportDir = TMP.'export_tests'.DS;

    public function setUp()
    {
        parent::setUp();

        Configure::write('Exports', [
            'path' => $this->testExportDir,
            'url'  => '/export_tests/',
            'maxSizeInBytes' => 0,
        ]);

        \Cake\Core\Plugin::load('Queue');

        $folder = new Folder($this->testExportDir);
        $folder->delete();
        $folder->create($this->testExportDir);

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
        return [
            'type' => 'list',
            'list_id' => 2,
            'fields' => ['id', 'lang', 'text'],
            'format' => 'tsv',
        ];
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

    public function testCreateListExport_returnsExport()
    {
        $expected = [
            'id' => 5,
            'name' => 'List Public list',
            'status' => 'queued',
        ];

        $export = $this->Exports->createExport(4, $this->options());

        $this->assertEquals($expected, $export);
    }

    public function testCreateExport_failsIfNoType()
    {
        $options = $this->optionsWithout(['type']);
        $result = $this->Exports->createExport(4, $options);
        $this->assertFalse($result);
    }

    public function testCreateExport_failsIfInvalidType()
    {
        $options = $this->optionsWith(['type' => 'invalid']);
        $result = $this->Exports->createExport(4, $options);
        $this->assertFalse($result);
    }

    public function testCreateExport_failsIfNoListId()
    {
        $options = $this->optionsWithout(['list_id']);
        $result = $this->Exports->createExport(4, $options);
        $this->assertFalse($result);
    }

    public function testCreateExport_failsIfInvalidListId()
    {
        $options = $this->optionsWith(['list_id' => 9999999999]);
        $result = $this->Exports->createExport(4, $options);
        $this->assertFalse($result);
    }

    public function testCreateExport_failsIfNoFields()
    {
        $options = $this->optionsWithout(['fields']);
        $result = $this->Exports->createExport(4, $options);
        $this->assertFalse($result);
    }

    public function testCreateExport_worksIfValidTransLang()
    {
        $options = $this->optionsWith(['trans_lang' => 'jpn']);
        $result = $this->Exports->createExport(4, $options);
        $this->assertNotFalse($result);
    }

    public function testCreateExport_failsIfInvalidTransLang()
    {
        $options = $this->optionsWith(['trans_lang' => 'invalid']);
        $result = $this->Exports->createExport(4, $options);
        $this->assertFalse($result);
    }

    public function testCreateExport_failsIfFieldsIsNotAnArray()
    {
        $options = $this->optionsWith(['fields' => 123]);
        $result = $this->Exports->createExport(4, $options);
        $this->assertFalse($result);
    }

    public function testCreateExport_failsIfFormatUnsupported()
    {
        $options = $this->optionsWith(['format' => 'message-in-a-bottle']);
        $result = $this->Exports->createExport(4, $options);
        $this->assertFalse($result);
    }

    public function testCreateExport_failsIfInvalidUserId()
    {
        $options = $this->options();
        $result = $this->Exports->createExport(9999999999, $options);
        $this->assertFalse($result);
    }

    public function testCreateExport_worksIfOwnedPrivateList()
    {
        $options = $this->optionsWith(['list_id' => 3]);
        $result = $this->Exports->createExport(7, $options);
        $this->assertTrue((bool)$result);
    }

    public function testCreateExport_failsIfOthersPrivateList()
    {
        $options = $this->optionsWith(['list_id' => 3]);
        $result = $this->Exports->createExport(4, $options);
        $this->assertFalse($result);
    }

    public function testCreateExport_worksIfUnlistedList()
    {
        $options = $this->optionsWith(['list_id' => 1]);
        $result = $this->Exports->createExport(4, $options);
        $this->assertTrue((bool)$result);
    }

    public function testCreateExport_worksIfPublicList()
    {
        $options = $this->options();
        $result = $this->Exports->createExport(4, $options);
        $this->assertTrue((bool)$result);
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
            'id' => 5,
            'name' => 'List Public list',
            'description' => 'Sentence id [tab] Sentence text',
            'generated' => null,
            'status' => 'queued',
            'pretty_filename' => null,
        ];
        $options = $this->options();

        $this->Exports->createExport(4, $options);

        $export = $this->Exports->find()->last()->toArray();
        $this->assertEquals($expected, $export);
    }

    public function testCreateExport_createsJob()
    {
        $this->Exports->deleteAll(['1=1']);
        $this->Exports->QueuedJobs->deleteAll(['1=1']);

        $this->Exports->createExport(4, $this->options());

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

        Time::setTestNow();
        $export = $this->Exports->get($exportId);
        $this->assertEquals($now, $export->generated);
        $this->assertEquals(TMP.'export_tests/list_3.tsv', $export->filename);
        $this->assertEquals('/export_tests/list_3.tsv', $export->url);
        $this->assertEquals('online', $export->status);
    }

    public function testRunExport_fileHasExpectedContents()
    {
        $jobId = 3;
        $exportId = 3;
        $config = (array)unserialize($this->Exports->QueuedJobs->get($jobId)->data);

        $this->Exports->runExport($config, $jobId);

        $filename = $this->Exports->get($exportId)->filename;
        $this->assertFileEquals(TESTS . 'Fixture'.DS.'list_3.tsv', $filename);
    }

    public function testRunExport_fileHasExpectedContents_withoutId()
    {
        $options = $this->optionsWith(['list_id' => 1, 'fields' => ['lang', 'text']]);
        $export = $this->Exports->createExport(7, $options);
        $config = (array)unserialize($this->Exports->QueuedJobs->find()->last()->data);
        $this->Exports->runExport($config);
        $firstExportId = $config['export_id'];

        $filename = $this->Exports->get($firstExportId)->filename;
        $this->assertFileEquals(TESTS . 'Fixture'.DS.'list_without_id.tsv', $filename);
    }

    public function testRunExport_fileHasExpectedContents_withTranslations()
    {
        $options = $this->optionsWith(['list_id' => 1, 'trans_lang' => 'jpn', 'fields' => ['id', 'text', 'trans_text']]);
        $export = $this->Exports->createExport(7, $options);
        $config = (array)unserialize($this->Exports->QueuedJobs->find()->last()->data);
        $this->Exports->runExport($config);
        $firstExportId = $config['export_id'];

        $filename = $this->Exports->get($firstExportId)->filename;
        $this->assertFileEquals(TESTS . 'Fixture'.DS.'list_with_translations.tsv', $filename);
    }

    public function testRunExport_pairs_fileHasExpectedContents()
    {
        $options = [
            'type' => 'pairs',
            'from' => 'fra',
            'to'   => 'jpn',
            'fields' => ['id', 'text', 'trans_id', 'trans_text'],
            'format' => 'tsv',
        ];
        $export = $this->Exports->createExport(7, $options);
        $config = (array)unserialize($this->Exports->QueuedJobs->find()->last()->data);
        $this->Exports->runExport($config);
        $firstExportId = $config['export_id'];

        $filename = $this->Exports->get($firstExportId)->filename;
        $this->assertFileEquals(TESTS . 'Fixture'.DS.'pairs.tsv', $filename);
    }

    public function testRunExport_fileHasExpectedContents_asTextFormat()
    {
        $options = $this->optionsWith(['list_id' => 1, 'fields' => ['text'], 'format' => 'txt']);
        $export = $this->Exports->createExport(7, $options);
        $config = (array)unserialize($this->Exports->QueuedJobs->find()->last()->data);
        $this->Exports->runExport($config);
        $firstExportId = $config['export_id'];

        $filename = $this->Exports->get($firstExportId)->filename;
        $this->assertFileEquals(TESTS . 'Fixture'.DS.'list_as_raw_text.txt', $filename);
    }

    public function testRunExport_fileHasExpectedContents_asShtookaFormat()
    {
        $options = $this->optionsWith(['list_id' => 1, 'format' => 'shtooka']);
        $export = $this->Exports->createExport(7, $options);
        $config = (array)unserialize($this->Exports->QueuedJobs->find()->last()->data);
        $this->Exports->runExport($config);
        $firstExportId = $config['export_id'];

        $filename = $this->Exports->get($firstExportId)->filename;
        $this->assertFileEquals(TESTS . 'Fixture'.DS.'list_for_shtooka.txt', $filename);
    }

    public function testRunExport_failsIfExportDirNotWritable()
    {
        $readOnlyDir = $this->testExportDir.'readonly';
        $folder = new Folder($readOnlyDir, true, 0444);
        if (is_writable($readOnlyDir)) {
            $this->markTestSkipped('Unable to create a read-only directory');
        }
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
        $config = $this->optionsWith(['list_id' => 1, 'export_id' => 999999999]);

        $result = $this->Exports->runExport($config, $jobId);

        $this->assertFalse($result);
    }

    public function testRunExport_failsIfInvalidListId()
    {
        $exportId = 3;
        $jobId = 3;
        $config = $this->optionsWith(['list_id' => 'invalid', 'export_id' => $exportId]);

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

    public function testRunExport_removesOldExports()
    {
        $options = $this->optionsWith(['list_id' => 3]);
        $export = $this->Exports->createExport(7, $options);
        $config = (array)unserialize($this->Exports->QueuedJobs->find()->last()->data);
        $this->Exports->runExport($config);
        $firstExportId = $config['export_id'];

        clearstatcache();
        $fileSize = filesize($this->Exports->get($firstExportId)->filename);
        $this->assertGreaterThan(0, $fileSize);
        Configure::write('Exports.maxSizeInBytes', $fileSize);

        $options = $this->optionsWith(['list_id' => 2]);
        $this->Exports->createExport(7, $options);
        $config = (array)unserialize($this->Exports->QueuedJobs->find()->last()->data);
        $this->Exports->runExport($config);

        try {
            $this->Exports->get($firstExportId);
            $this->fail('Export is not deleted');
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    public function testRemovingExportAlsoRemovesFile()
    {
        $options = $this->optionsWith(['list_id' => 3]);
        $this->Exports->createExport(7, $options);
        $config = (array)unserialize($this->Exports->QueuedJobs->find()->last()->data);
        $this->Exports->runExport($config);
        $export = $this->Exports->get($config['export_id']);
        $this->assertFileExists($export->filename);

        $this->Exports->delete($export);

        $this->assertFileNotExists($export->filename);
    }
}
