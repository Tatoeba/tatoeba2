<?php
namespace App\Test\TestCase\Shell\Task;

use App\Shell\Task\QueueSwitchSentencesLicenseTask;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOutput;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Core\Plugin;
use Cake\Utility\Hash;
use Cake\Core\Configure;
use App\Model\CurrentUser;

class QueueSwitchSentencesLicenseTaskTest extends TestCase
{
    public $fixtures = array(
        'app.sentences',
        'app.contributions',
        'app.reindex_flags',
        'app.users_languages',
        'app.private_messages',
        'app.links',
        'app.users',
    );

    public function setUp()
    {
        parent::setUp();
        Configure::write('Acl.database', 'test');
        Plugin::load('Queue');

        $io = $this->getMockBuilder(ConsoleIo::class)->getMock();
        $this->task = $this->getMockBuilder(QueueSwitchSentencesLicenseTask::class)
            ->setMethods(['in', 'err', 'createFile', '_stop', 'clear'])
            ->setConstructorArgs([$io])
            ->getMock();
        $this->task->batchOperationSize = 10;

        $this->Sentences = TableRegistry::getTableLocator()->get('Sentences');
        $this->PrivateMessages = TableRegistry::getTableLocator()->get('PrivateMessages');
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->task);
    }

    public function testSwitchLicense()
    {
        $before = $this->Sentences->findAllByLicense('CC0 1.0')->toList();
        $beforeIds = Hash::extract($before, '{n}.id');
        $options = array(
            'userId' => 4,
            'dryRun' => false,
        );
        $expected = array(48, 53);

        $this->task->run($options);

        $after = $this->Sentences->findAllByLicense('CC0 1.0')->toList();
        $afterIds = Hash::extract($after, '{n}.id');
        $switched = array_diff($afterIds, $beforeIds);
        sort($switched);

        $this->assertNotEquals($before, $after);
        $this->assertEquals($expected, $switched);
    }

    public function testSwitchLicense_withDryRun()
    {
        $before = $this->Sentences->findAllByLicense('CC0 1.0');
        $options = array(
            'userId' => 4,
            'dryRun' => true,
        );

        $this->task->run($options);

        $after = $this->Sentences->findAllByLicense('CC0 1.0');
        $this->assertEquals($before, $after);
    }

    public function testSwitchLicense_sendsResultByPM()
    {
        $options = array(
            'userId' => 4,
            'dryRun' => false,
            'sendReport' => true,
        );
        CurrentUser::store(['id' => 4]);
        $numPmBefore = $this->PrivateMessages->find('all')->count();
        $this->task->run($options);
        CurrentUser::store(['id' => 4]);
        $numPmAfter = $this->PrivateMessages->find('all')->count();

        $this->assertEquals(1, $numPmAfter - $numPmBefore);
    }

    public function testSwitchLicense_reportsErrors() {
        $fakeError = "AAAAAH I'M DYIIIING";
        $rule = function() use ($fakeError) {
            return $fakeError;
        };
        $validator = $this->Sentences->getValidator();
        $validator->add('license', 'always-fail', compact('rule'));

        $options = array(
            'userId' => 4,
            'dryRun' => true,
            'sendReport' => true,
        );
        $this->task->run($options);

        $this->assertContains($fakeError, $this->task->getReport());
    }

    public function testSwitchLicense_batchedOperation() {
        $this->task->batchOperationSize = 1;
        $this->testSwitchLicense();
    }
}
