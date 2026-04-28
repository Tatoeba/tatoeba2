<?php

namespace App\Shell\Task;

use Queue\Shell\Task\QueueTask;

class QueueExportTask extends QueueTask {
    public $retries = 0;

    public function run(array $config, int $jobId): void {
        $this->loadModel('Exports');
        $this->Exports->runExport($config, $jobId);
    }
}
