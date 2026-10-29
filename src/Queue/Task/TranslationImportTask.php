<?php
namespace App\Queue\Task;

use Queue\Queue\Task;

class TranslationImportTask extends Task
{
    public $retries = 3;
    public $timeout = 600;

    public function run(array $config, int $jobId): void
    {
        $this->fetchTable('TranslationImports')->runBatch(
            (int)$config['import_id'],
            $jobId
        );
    }
}