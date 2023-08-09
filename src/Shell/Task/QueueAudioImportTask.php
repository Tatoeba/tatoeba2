<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2022 Gilles Bedel
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\Shell\Task;

use Queue\Shell\Task\QueueTask;

class QueueAudioImportTask extends QueueTask {
    public $retries = 0;

    public function run(array $config, $jobId) {
        $this->loadModel('Audios');
        $errors = false;
        $filesImported = $this->Audios->importFiles($errors, $config);

        $this->loadModel('Queue.QueuedJobs');
        $me = $this->QueuedJobs->get($jobId);
        $me->failure_message = serialize(compact('filesImported', 'errors'));
        $this->QueuedJobs->save($me);

        return true;
    }
}
