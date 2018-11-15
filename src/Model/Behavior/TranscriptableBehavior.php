<?php
/**
 * Tatoeba Project, free collaborative creation of languages corpuses project
 * Copyright (C) 2015  Gilles Bedel
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
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\Event\Event;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;

/**
 * Model behavior for transcriptions/transliterations.
 * Only attached to the Sentence model.
 */
class TranscriptableBehavior extends Behavior
{
    public function initialize(array $config) {
        $this->Transcriptions = TableRegistry::getTableLocator()->get('Transcriptions');
    }

    public function beforeSave($event, $entity, $options) {
        $entity->script = $this->Transcriptions->detectScript(
            $entity->lang, 
            $entity->text
        );
        if (!$this->isScriptValid($entity)) {
            return false;
        }
        return true;
    }

    private function isScriptValid($entity) {
        if (!isset($entity->script))
            return true;

        $isValid = $this->Transcriptions->isValidScriptForLanguage(
            $entity->lang, 
            $entity->script
        );
        return $isValid;
    }

    public function afterSave($event, $entity, $options) {
        if ($entity->text || $entity->lang) {
            $this->deleteTranscriptions($entity);
        }
        $this->createTranscriptions($entity);
    }

    private function createTranscriptions($entity) {
        $this->Transcriptions->generateAndSaveAllTranscriptionsFor($entity->old_format);
    }

    private function deleteTranscriptions($entity) {
        $this->Transcriptions->deleteAll(
            array('Transcription.sentence_id' => $entity->id),
            false,
            false
        );
    }

    public function afterFind(Model $model, $results, $primary = false) {
        foreach ($results as &$result) {
            if ($primary) {
                if (isset($result[$model->alias])) {
                    $sentence = $result[$model->alias];
                } else {
                    continue;
                }
            } else {
                $sentence = $result;
            }

            /* Add script on the fly if missing */
            if (isset($result['Sentence'])
                && !isset($sentence['script'])
                && isset($sentence['lang'])
                && isset($sentence['text'])) {
                $sentence['script'] = $model->Transcription->detectScript(
                    $sentence['lang'],
                    $sentence['text']
                );
            }

            /* Add transcriptions on the fly if missing */
            if (isset($result['Transcription'])
                && isset($sentence['lang'])
                && isset($sentence['text'])) {
                $result['Transcription'] =
                    $model->Transcription->addGeneratedTranscriptions(
                        $result['Transcription'], $sentence
                    );
            }
        }
        return $results;
    }
}
