<?php

namespace App\Shell\Task;

use Queue\Shell\Task\QueueTask;

class QueueExportTask extends QueueTask {
    public $retries = 0;

    public function run(array $config, $jobId) {
        $this->loadModel('Exports');
        return $this->Exports->runExport($config, $jobId);
    }
}
