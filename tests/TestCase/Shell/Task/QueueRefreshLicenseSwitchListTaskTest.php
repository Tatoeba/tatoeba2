<?php
namespace App\Test\TestCase\Shell\Task;

use App\Shell\Task\QueueRefreshLicenseSwitchListTask;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOutput;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

class QueueRefreshLicenseSwitchListTaskTest extends TestCase
{
    public $fixtures = array(
        'app.Sentences',
        'app.Contributions',
        'app.SentencesLists',
        'app.SentencesSentencesLists',
    );

    public function setUp(): void
    {
        parent::setUp();
        parent::loadPlugins(['Queue']);

        $io = $this->getMockBuilder(ConsoleIo::class)->getMock();
        $this->task = $this->getMockBuilder(QueueRefreshLicenseSwitchListTask::class)
            ->setMethods(['in', 'err', 'createFile', '_stop', 'clear'])
            ->setConstructorArgs([$io])
            ->getMock();

        $this->SentencesLists = $this->getTableLocator()->get('SentencesLists');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->task);
    }

    public function testRefreshList()
    {
        $userId = 4;
        $list = $this->SentencesLists->createList('list name', $userId);
        $listId = $list->id;
        $expected = array(48, 53);

        $this->task->run(compact('userId', 'listId'), 1234);

        $after = $this->SentencesLists->SentencesSentencesLists->find()
            ->where(['sentences_list_id' => $listId])
            ->toList();
        $afterIds = Hash::extract($after, '{n}.sentence_id');

        $this->assertEquals($expected, $afterIds);
    }

    public function testRefreshList_batchedOperation() {
        $this->task->batchOperationSize = 1;
        $this->testRefreshList();
    }

    public function testRefreshList_doesNotFailOnDuplicateLogRows()
    {
        $Contributions = $this->getTableLocator()->get('Contributions');
        $row = $Contributions->find()
            ->where([
                'sentence_id' => 48,
                'type' => 'sentence',
                'action' => 'insert',
            ])
            ->first();
        $row->id = null;
        $row->setNew(true);
        $Contributions->save($row);

        $this->testRefreshList();
    }
}
