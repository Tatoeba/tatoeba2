<?php
namespace App\Test\TestCase\Queue\Task;

use App\Queue\Task\RefreshLicenseSwitchListTask;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

class RefreshLicenseSwitchListTaskTest extends TestCase
{
    public $fixtures = array(
        'app.Sentences',
        'app.Contributions',
        'app.SentencesLists',
        'app.SentencesSentencesLists',
    );

    private $SentencesLists;
    private $task;

    public function setUp(): void
    {
        parent::setUp();
        parent::loadPlugins(['Queue']);

        $this->task = new RefreshLicenseSwitchListTask();
        $this->SentencesLists = $this->fetchTable('SentencesLists');
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
            ->all()
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
        $Contributions = $this->fetchTable('Contributions');
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
