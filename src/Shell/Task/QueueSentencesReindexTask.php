<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2025 Gilles Bedel
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

use Cake\Datasource\ModelAwareTrait;
use Queue\Shell\Task\QueueTask;

class QueueSentencesReindexTask extends QueueTask {

    use ModelAwareTrait;

    public $retries = 0;

    private function reindexNativeSpeakerSentences(int $user_id, string $lang) {
        $this->loadModel('Sentences');
        $sentenceIds = $this->Sentences
            ->find('list', ['valueField' => 'id'])
            ->where(compact('user_id', 'lang'))
            ->toList();
        $this->Sentences->flagSentenceAndTranslationsToReindex($sentenceIds);
    }

    public function run(array $config, $jobId) {
        $this->reindexNativeSpeakerSentences($config['user_id'], $config['lang']);
        return true;
    }
}
