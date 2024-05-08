<?php

namespace App\Shell\Task;

use App\Model\CurrentUser;
use App\Shell\BatchOperationTrait;
use Cake\Console\Shell;
use Cake\I18n\I18n;
use Queue\Shell\Task\QueueTask;

class QueueSwitchSentencesLicenseTask extends QueueTask {

    use BatchOperationTrait;

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

    public function getReport() {
        return $this->report;
    }

    private function sendReport($recipientId) {
        $this->loadModel('PrivateMessages');
        $now = date("Y/m/d H:i:s", time());
        $data = [
            'title' => __('Result of license switch to CC0 1.0'),
            'content' => $this->getReport(),
            'messageId' => '',
        ];
        $this->PrivateMessages->notify($recipientId, $now, $data);
    }

    private function _switchLicense($entities, $options) {
        $total = 0;
        $newLicense = 'CC0 1.0';
        foreach ($entities as $ent) {
            $id = $ent->id;
            $data = $this->Sentences->get($id);
            $this->Sentences->patchEntity($data, ['license' => $newLicense]);
            $ok = $this->Sentences->save($data);
            if ($ok) {
                $total++;
                $this->Sentences->SentencesLists->removeSentenceFromList(
                    $id,
                    $options['listId'],
                    $options['userId']
                );
            } else {
                $message = format(
                    __("Unable to change the license of sentence {id} to “{newLicense}” because:"),
                    compact('id', 'newLicense')
                );
                $errors = $data->getError('license');
                $this->out($message."\n - ".implode("\n - ", $errors));
            }
        }
        return $total;
    }

    private function dateAndTime() {
        $now = time();
        return array(
            'date' => gmdate('Y-m-d', $now),
            'time' => gmdate('H:i:s', $now),
        );
    }

    private function switchLicense($options) {
        $this->loadModel('Sentences');
        $query = $this->Sentences->find()
             ->select(['id' => 'Sentences.id'])
             ->matching('SentencesLists', function ($q) use ($options) {
                 return $q->where(['SentencesLists.id' => $options['listId']]);
             });

        $this->out(format(
            __('License switch started on {date} at {time} UTC.'),
            $this->dateAndTime()
        ));
        $proceeded = $this->batchOperationNewORM($query, [$this, '_switchLicense'], $options);
        $this->out(format(
            __n('Successfully changed the license of {n} sentence.',
                'Successfully changed the license of {n} sentences.',
                $proceeded),
            array('n' => $proceeded)
        ));

        $this->out(format(
            __('License switch completed on {date} at {time} UTC.'),
            $this->dateAndTime()
        ));
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
        if (isset($options['locale'])) {
            $prevLocale = I18n::getLocale();
            I18n::setLocale($options['locale']);
        }

        if (isset($options['sendReport'])) {
            $this->sendReport = $options['sendReport'];
        }

        CurrentUser::store(array('id' => $options['userId']));
        $this->switchLicense($options);
        CurrentUser::store(null);

        if ($this->sendReport) {
            $this->sendReport($options['userId']);
        }

        if (isset($prevLocale)) {
            I18n::setLocale($prevLocale);
        }
        return true;
    }
}
