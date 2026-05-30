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

use App\Shell\BatchOperationTrait;
use Cake\ORM\Locator\LocatorAwareTrait;
use Queue\Shell\Task\QueueTask;

class QueueSentencesReindexTask extends QueueTask {

    use BatchOperationTrait;
    use LocatorAwareTrait;

    public $retries = 0;

    public function __construct() {
        parent::__construct(...func_get_args());

        /* 500 is just my guess of a reasonable balance between processing
         * efficency and RAM usage. The number of sentences queued for
         * reindexation will be about 500 multiplied by the average number
         * of direct and indirect translations, which, in practice, should
         * not be greater than tens of thousands. */
        $this->batchOperationSize = 500;
    }

    private function reindexNativeSpeakerSentences(int $user_id, string $lang) {
        $Sentences = $this->fetchTable('Sentences');
        $query = $Sentences
            ->find()
            ->select(['id' => 'Sentences.id'])
            ->where(compact('user_id', 'lang'));

        $this->batchOperationNewORM($query, function ($entities) use ($Sentences) {
            $sentenceIds = $entities->extract('id')->toList();
            if (!empty($sentenceIds)) {
                $Sentences->flagSentenceAndTranslationsToReindex($sentenceIds);
            }
        });
    }

    public function run(array $config, int $jobId): void {
        $this->reindexNativeSpeakerSentences($config['user_id'], $config['lang']);
    }
}
