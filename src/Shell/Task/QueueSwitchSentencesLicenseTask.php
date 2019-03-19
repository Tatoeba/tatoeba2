<?php

namespace App\Shell\Task;

use App\Model\CurrentUser;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Queue\Shell\Task\QueueTask;
use App\Shell\AppShell;

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
        $this->loadModel('Users');
        $user = $this->Users->findByUsername($username)->first();
        if (!$user) {
            if (!empty($username)) {
                $this->out("Error: '$username' is not a valid username.");
            }
            $this->out('Usage: cake queue add SwitchSentencesLicense <username> [dryrun]');
            return;
        }

        $userId = $user->id;
        $options = compact('userId', 'dryRun');
        if ($this->QueuedJobs->createJob('SwitchSentencesLicense', $options)) {
            $this->out('OK, job created, now run the worker');
        } else {
            $this->err('Could not create Job');
        }
    }

    private function sendReport($recipientId) {
        $this->loadModel('PrivateMessages');
        $now = date("Y/m/d H:i:s", time());
        $data = [
            'title' => __('Result of license switch to CC0 1.0'),
            'content' => $this->report,
            'messageId' => '',
        ];
        $this->PrivateMessages->notify($recipientId, $now, $data);
    }

    public function _switchLicense($rows, $modelName, $dryRun) {
        $total = 0;
        $saveParams = array(
            'validate' => true,
            'callbacks' => true,
        );
        $newLicense = 'CC0 1.0';
        foreach ($rows as $row) {
            $id = $row->id;
            $data = $this->{$modelName}->get($id);
            if ($dryRun) {
                $this->{$modelName}->patchEntity($data, ['license' => $newLicense]);
                $ok = empty($data->getErrors());                
            } else {
                $this->{$modelName}->patchEntity($data, ['license' => $newLicense]);
                $ok = $this->{$modelName}->save($data);
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

    private function dateAndTime() {
        $now = time();
        return array(
            'date' => gmdate('Y-m-d', $now),
            'time' => gmdate('H:i:s', $now),
        );
    }

    private function switchLicense($options) {
        $this->loadModel('Sentences');
        $findOptions = array(
            'fields' => array('Sentences.id'),
            'conditions' => array(
                'license' => 'CC BY 2.0 FR',
                'Sentences.user_id' => $options['userId'],
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

        $this->out(format(
            __('License switch started on {date} at {time} UTC.'),
            $this->dateAndTime()
        ));
        $selected = $this->Sentences->find('all', $findOptions)->count();
        $this->out(format(
            __n('Found {n} sentence that can be switched to {newLicense}.',
                'Found {n} sentences that can be switched to {newLicense}.',
                $selected),
            array('n' => $selected,
                  'newLicense' => 'CC0 1.0')
        ));

        if ($selected > 0) {
            $proceeded = $this->batchOperation(
                'Sentences',
                '_switchLicense',
                $findOptions,
                $options['dryRun']
            );
            $this->out(format(
                __n('Successfully changed the license of {n} sentence.',
                    'Successfully changed the license of {n} sentences.',
                    $proceeded),
                array('n' => $proceeded)
            ));
        }
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
        if (isset($options['UIlang'])) {
            $prevLang = Configure::read('Config.language');
            Configure::write('Config.language', $options['UIlang']);
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

        if (isset($prevLang)) {
            Configure::write('Config.language', $prevLang);
        }
        return true;
    }


    // ----------------------------------------------------
    // TODO Refactor. 
    // Below is a copy-paste of AppShell.php as a quick
    // solution to migrate the mass license update feature.
    // ----------------------------------------------------

    public $batchOperationSize = 1000;

    private function _orderCondition($nonUniqueField, $lastValue, $pKey, $lastId) {
        if ($nonUniqueField == $pKey) {
            return array("$pKey >" => $lastId);
        } else {
            return array('AND' => array(
                "$nonUniqueField >=" => $lastValue,
                array('OR' => array(
                    "$nonUniqueField >" => $lastValue,
                    array('AND' => array(
                        $nonUniqueField => $lastValue,
                        "$pKey >" => $lastId,
                    )),
                )),
            ));
        }
    }

    protected function batchOperation($model, $operation, $options) {
        if (!isset($options['order'])) {
            $options['order'] = $this->{$model}->getAlias().'.id';
        }
        if (is_string($options['order'])) {
            $options['order'] = array($options['order']);
        }
        $order1 = $options['order'][0];
        if (count($options['order']) == 2) {
            $order2 = $options['order'][1];
        } else {
            $order2 = $order1;
        }
        if (isset($options['fields'])) {
            foreach ($options['order'] as $field) {
                $options['fields'][] = $field;
            }
        }

        $o1parts = explode('.', $order1);
        $o2parts = explode('.', $order2);
        $proceeded = 0;
        $options = array_merge(
            array(
                'contain' => array(),
                'limit' => $this->batchOperationSize,
            ),
            $options
        );

        if (!isset($options['conditions'])) {
            $options['conditions'] = array();
        }
        $options['conditions'][] = array();
        end($options['conditions']);
        $conditionKey = key($options['conditions']);
        reset($options['conditions']);

        $data = array();
        do {
            $data = $this->{$model}->find('all', $options)->toList();
            $args = func_get_args();
            array_splice($args, 0, 3, array($data, $model));
            $proceeded += call_user_func_array(array($this, $operation), $args);
            $lastRow = end($data);
            if ($lastRow) {
                $lastValue1 = $lastRow[ $o1parts[0] ][ $o1parts[1] ];
                $lastValue2 = $lastRow[ $o2parts[0] ][ $o2parts[1] ];
                $options['conditions'][$conditionKey] = $this->_orderCondition($order1, $lastValue1, $order2, $lastValue2);
            }
        } while ($data);
        return $proceeded;
    }
}
