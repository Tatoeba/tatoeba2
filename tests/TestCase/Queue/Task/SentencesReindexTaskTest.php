<?php
namespace App\Test\TestCase\Queue\Task;

use App\Queue\Task\SentencesReindexTask;
use Cake\TestSuite\TestCase;

class SentencesReindexTaskTest extends TestCase
{
    public $fixtures = [
        'app.ReindexFlags',
        'app.Sentences',
        'app.Links',
    ];
    public $task;

    public function setUp(): void
    {
        parent::setUp();
        $this->task = new SentencesReindexTask();
    }

    public function tearDown(): void
    {
        unset($this->task);
        parent::tearDown();
    }

    private function assertSentencesFlaggedForReindex($expected)
    {
        $result = $this->fetchTable('ReindexFlags')
            ->find('list', ['valueField' => 'sentence_id'])
            ->select(['sentence_id'])
            ->order('sentence_id')
            ->all()
            ->toList();
        $this->assertEquals($expected, $result);
    }

    public function testRun_userLanguageLevelUp()
    {
        $this->task->run(['user_id' => 7, 'lang' => 'fra'], 1234);

        // should reindex sentences 4, 8, 12, 23 and 35
        // along with all direct and indirect translations
        $this->assertSentencesFlaggedForReindex([1, 2, 3, 4, 5, 6, 8, 10, 12, 23, 35]);
    }

    public function testRun_userLanguageLevelDrop()
    {
        $this->task->run(['user_id' => 7, 'lang' => 'jpn'], 1234);

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
