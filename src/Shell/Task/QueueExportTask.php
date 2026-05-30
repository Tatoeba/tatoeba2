<?php

namespace App\Shell\Task;

use Queue\Shell\Task\QueueTask;

class QueueExportTask extends QueueTask {
    public $retries = 0;

    public function run(array $config, int $jobId): void {
        $this->fetchTable('Exports')->runExport($config, $jobId);
    }
}
