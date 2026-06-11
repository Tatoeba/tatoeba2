<?php

namespace App\Queue\Task;

use Queue\Queue\Task;

class ExportTask extends Task {
    public $retries = 0;

    public function run(array $config, int $jobId): void {
        $this->fetchTable('Exports')->runExport($config, $jobId);
    }
}
