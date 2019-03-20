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

    public function add() {
        $username = isset($this->args[1]) ? $this->args[1] : '';
        $this->loadModel('Users');
        $user = $this->Users->findByUsername($username)->first();
        if (!$user) {
            if (!empty($username)) {
                $this->out("Error: '$username' is not a valid username.");
            }
            $this->out('Usage: cake queue add RefreshLicenseSwitchList <username>');
            return;
        }

        $userId = $user->id;
        $options = compact('userId');
        if ($this->QueuedJobs->createJob('RefreshLicenseSwitchList', $options)) {
            $this->out('OK, job created, now run the worker');
        } else {
            $this->err('Could not create Job');
        }
    }

    public function addToList($sentences, $modelName, $listId, $userId) {
        $this->SentencesLists->addSentencesToList($sentences, $listId, $userId);
        return count($sentences);
    }

    private function refreshList($options) {
        extract($options); // $listId and $userId

        $this->loadModel('Sentences');
        $findOptions = array(
            'fields' => array('Sentences.id'),
            'conditions' => array(
                'license' => 'CC BY 2.0 FR',
                'Sentences.user_id' => $userId,
                'based_on_id' => 0,
            ),
            'join' => array(array(
                'table' => 'contributions',
                'alias' => 'Contributions',
                'type' => 'INNER',
                'conditions' => array(
                    'Contributions.sentence_id = Sentences.id',
                    'Contributions.user_id = Sentences.user_id',
                    'action' => 'insert',
                    'type' => 'sentence',
                ),
            )),
        );

        $this->loadModel('SentencesLists');
        $this->SentencesLists->emptyList($listId, $userId);

        $proceeded = $this->batchOperation(
            'Sentences',
            'addToList',
            $findOptions,
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
