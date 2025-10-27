<?php
namespace App\Test\TestCase\Shell\Task;

use App\Shell\Task\QueueSentencesReindexTask;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class QueueSentencesReindexTaskTest extends TestCase
{
    public $fixtures = [
        'app.reindex_flags',
        'app.sentences',
        'app.links',
    ];
    public $io;
    public $task;

    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();
        $this->task = new QueueSentencesReindexTask($this->io);
    }

    public function tearDown()
    {
        unset($this->task);
        parent::tearDown();
    }

    private function assertSentencesFlaggedForReindex($expected)
    {
        $result = TableRegistry::getTableLocator()->get('ReindexFlags')
            ->find('list', ['valueField' => 'sentence_id'])
            ->select(['sentence_id'])
            ->order('sentence_id')
            ->toList();
        $this->assertEquals($expected, $result);
    }

    public function testRun_userLanguageLevelUp()
    {
        $this->task->run(['user_id' => 7, 'lang' => 'fra'], null);

        // should reindex sentences 4, 8, 12 and 35
        // along with all direct and indirect translations
        $this->assertSentencesFlaggedForReindex([1, 2, 3, 4, 5, 6, 8, 10, 12, 35]);
    }

    public function testRun_userLanguageLevelDrop()
    {
        $this->task->run(['user_id' => 7, 'lang' => 'jpn'], null);

        // should reindex sentences 6, 10, 56 and 57
        // along with all direct and indirect translations
        $this->assertSentencesFlaggedForReindex([1, 2, 4, 6, 10, 55, 56, 57, 65]);
    }

    public function testRun_userLanguageLevelUp_batched()
    {
        $this->task->batchOperationSize = 2;
        $this->testRun_userLanguageLevelUp();
    }
}
