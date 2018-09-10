<?php

App::uses('QueueTask', 'Queue.Console/Command/Task');
App::uses('Model', 'CurrentUser');

class QueueSwitchSentencesLicenseTask extends QueueTask {

    public $uses = array(
        'Sentence',
        'User',
        'PrivateMessage',
    );

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

    private $sendReport = false;
    private $report = '';

    public function out($message = null, $newlines = 1, $level = Shell::NORMAL) {
        if ($this->sendReport) {
            $this->report .= $message;
            if ($newlines) {
                $this->report .= PHP_EOL;
            }
        }
        return parent::out($message, $newlines, $level);
    }

    public function add() {
        $username = isset($this->args[1]) ? $this->args[1] : '';
        $dryRun = isset($this->args[2]) && $this->args[2] == 'dryrun';
        $user = $this->User->findByUsername($username, 'id');
        if (!$user) {
            if (!empty($username)) {
                $this->out("Error: '$username' is not a valid username.");
            }
            $this->out('Usage: cake Queue.Queue add SwitchSentencesLicense <username> [dryrun]');
            return;
        }

        $userId = $user['User']['id'];
        $options = compact('userId', 'dryRun');
        if ($this->QueuedTask->createJob('SwitchSentencesLicense', $options)) {
            $this->out('OK, job created, now run the worker');
        } else {
            $this->err('Could not create Job');
        }
    }

    private function sendReport($recipientId) {
        $now = date("Y/m/d H:i:s", time());
        $data = array('PrivateMessage' => array(
            'title' => __('Result of license switch to CC0 1.0'),
            'content' => $this->report,
            'messageId' => '',
        ));
        $message = $this->PrivateMessage->buildMessage($data, 0, $now);
        $this->PrivateMessage->saveToInbox($message, $recipientId);
    }

    protected function switchLicense($rows, $modelName, $dryRun) {
        $total = 0;
        $saveParams = array(
            'validate' => true,
            'callbacks' => true,
        );
        $newLicense = 'CC0 1.0';
        foreach ($rows as $row) {
            $id = $row[$modelName]['id'];
            $this->{$modelName}->id = $id;
            if ($dryRun) {
                $this->{$modelName}->set(array('license' => $newLicense));
                $ok = $this->{$modelName}->validates();
            } else {
                $ok = $this->{$modelName}->saveField('license', $newLicense, $saveParams);
            }
            if ($ok) {
                $total++;
            } else {
                $message = format(
                    __("Unable to change the license of sentence {id} to “{newLicense}” because:"),
                    compact('id', 'newLicense')
                );
                $errors = $this->Sentence->validationErrors['license'];
                $this->out($message."\n - ".implode("\n - ", $errors));
            }
        }
        return $total;
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
    public function run($options, $id = null) {
        if (isset($options['UIlang'])) {
            $prevLang = Configure::read('Config.language');
            Configure::write('Config.language', $options['UIlang']);
        }

        if (isset($options['sendReport'])) {
            $this->sendReport = $options['sendReport'];
        }

        $findOptions = array(
            'fields' => array('Sentence.id'),
            'conditions' => array(
                'license' => 'CC BY 2.0 FR',
                'Sentence.user_id' => $options['userId'],
                'based_on_id' => 0,
            ),
            'joins' => array(array(
                'table' => 'contributions',
                'alias' => 'Contributions',
                'type' => 'INNER',
                'conditions' => array(
                    'Contributions.sentence_id = Sentence.id',
                    'Contributions.user_id = Sentence.user_id',
                    'action' => 'insert',
                    'type' => 'sentence',
                ),
            )),
        );

        CurrentUser::store(array('id' => $options['userId']));

        $selected = $this->Sentence->find('count', $findOptions);
        $this->out(format(
            __n('Found {n} sentence that can be switched to {newLicense}.',
                'Found {n} sentences that can be switched to {newLicense}.',
                $selected),
            array('n' => $selected,
                  'newLicense' => 'CC0 1.0')
        ));

        $proceeded = $this->batchOperation(
            'Sentence',
            'switchLicense',
            $findOptions,
            $options['dryRun']
        );
        $this->out(format(
            __n('Changed the license of {n} sentence.',
                'Changed the license of {n} sentences.',
                $proceeded),
            array('n' => $proceeded)
        ));
        CurrentUser::store(null);

        if ($this->sendReport) {
            $this->sendReport($options['userId']);
        }

        if (isset($prevLang)) {
            Configure::write('Config.language', $prevLang);
        }
        return true;
    }
}
