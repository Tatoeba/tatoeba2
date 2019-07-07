<?php

namespace App\Shell\Task;

use App\Shell\BatchOperationTrait;
use Queue\Shell\Task\QueueTask;

class QueueRefreshLicenseSwitchListTask extends QueueTask {

    use BatchOperationTrait;

/**
 * ZendStudio Codecomplete Hint
 *
 * @var QueuedTask
 */
    public $QueuedTask;

/**
 * Timeout for run, after which the Task is reassigned to a new worker.
 *
 * @var int
 */
    public $timeout = 10;

/**
 * Number of times a failed instance of this task should be restarted before giving up.
 *
 * @var int
 */
    public $retries = 1;

/**
 * Stores any failure messages triggered during run()
 *
 * @var string
 */
    public $failureMessage = '';

    public function addToList($sentences, $listId, $userId) {
        $this->SentencesLists->addSentencesToList($sentences, $listId, $userId);
        return count($sentences);
    }

    private function refreshList($options) {
        extract($options); // $listId and $userId

        $this->loadModel('Sentences');
        $query = $this->Sentences->find()
            ->select('Sentences.id')->where([
                'license' => 'CC BY 2.0 FR',
                'Sentences.user_id' => $userId,
                'based_on_id' => 0,
            ])
            ->innerJoinWith('Contributions', function ($q) {
                return $q->where([
                    'Contributions.user_id = Sentences.user_id',
                    'action' => 'insert',
                    'type' => 'sentence',
                ]);
            });

        $this->loadModel('SentencesLists');
        $this->SentencesLists->emptyList($listId, $userId);

        $proceeded = $this->batchOperationNewORM(
            $query,
            [$this, 'addToList'],
            $listId,
            $userId
        );
    }

/**
 * Example run function.
 * This function is executed, when a worker is executing a task.
 * The return parameter will determine, if the task will be marked completed, or be requeued.
 *
 * @param array $data The array passed to QueuedTask->createJob()
 * @param int $id The id of the QueuedTask
 * @return bool Success
 */
    public function run(array $options, $id = null) {
        $this->refreshList($options);
        return true;
    }
}
