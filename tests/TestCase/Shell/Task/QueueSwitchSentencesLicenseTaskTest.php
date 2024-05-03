<?php
namespace App\Test\TestCase\Shell\Task;

use App\Shell\Task\QueueSwitchSentencesLicenseTask;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOutput;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use App\Model\CurrentUser;

class QueueSwitchSentencesLicenseTaskTest extends TestCase
{
    public $fixtures = array(
        'app.sentences',
        'app.sentences_lists',
        'app.sentences_sentences_lists',
        'app.users_languages',
        'app.contributions',
        'app.reindex_flags',
        'app.private_messages',
    );

    public function setUp()
    {
        parent::setUp();
        parent::loadPlugins(['Queue']);

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

    public function _testSwitchLicense($expected, $options)
    {
        $before = $this->Sentences->findAllByLicense('CC0 1.0')->toList();
        $beforeIds = Hash::extract($before, '{n}.id');

        $this->task->run($options);

        $after = $this->Sentences->findAllByLicense('CC0 1.0')->toList();
        $afterIds = Hash::extract($after, '{n}.id');
        $switched = array_diff($afterIds, $beforeIds);
        sort($switched);

        $this->assertNotEquals($before, $after);
        $this->assertEquals($expected, $switched);
    }

    public function testSwitchLicense_all()
    {
        $expected = [48, 53];
        $options = ['userId' => 4, 'listId' => 4];
        $this->_testSwitchLicense($expected, $options);
    }

    public function testSwitchLicense_partial()
    {
        $this->Sentences->SentencesLists->removeSentenceFromList(48, 4, 4);
        $expected = [53];
        $options = ['userId' => 4, 'listId' => 4];
        $this->_testSwitchLicense($expected, $options);
    }

    public function testSwitchLicense_sendsResultByPM()
    {
        $options = ['userId' => 4, 'listId' => 4, 'sendReport' => true];
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

        $options = ['userId' => 4, 'listId' => 4, 'sendReport' => true];

        $this->task->run($options);

        $this->assertContains($fakeError, $this->task->getReport());
    }

    public function testSwitchLicense_batchedOperation() {
        $this->task->batchOperationSize = 1;
        $this->testSwitchLicense_all();
    }

    public function testSwitchLicense_removesSentencesFromList()
    {
        $options = ['userId' => 4, 'listId' => 4];

        $this->task->run($options);

        $count = $this->Sentences->SentencesLists->getNumberOfSentences(4);
        $this->assertEquals(0, $count);
    }
}
