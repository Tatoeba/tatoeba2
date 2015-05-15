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

/**
 * Model behavior for transcriptions/transliterations.
 * Only attached to the Sentence model.
 */
class TranscriptableBehavior extends ModelBehavior
{
    private function addScriptInformation($model) {
        $script = $model->Transcription->detectScript(
            $model->data[$model->alias]['lang'],
            $model->data[$model->alias]['text']
        );
        $model->data[$model->alias]['script'] = $script;
    }

    public function beforeSave(&$model) {
        if (!$model->id) { // adding a new sentence
            $this->addScriptInformation($model);
        }
        return true;
    }

    public function afterDelete(&$model) {
        $conditions = array('Transcription.sentence_id' => $model->id);
        $model->Transcription->deleteAll($conditions, false);
    }

    public function afterSave(&$model, $created) {
        if ($created) {
            $model->data[$model->alias]['id'] = $model->getLastInsertID();
            $this->createTranscriptions($model);
        } else {
            if (array_key_exists('text', $model->data[$model->alias]) ||
                array_key_exists('lang', $model->data[$model->alias])) {
                $this->deleteTranscriptions($model);
                $model->read();
                $this->createTranscriptions($model);
            }
        }
    }

    private function createTranscriptions($model) {
        $sentence = $model->data[$model->alias];
        $model->Transcription->generateAndSaveAllTranscriptionsFor($sentence);
    }

    private function deleteTranscriptions($model) {
        $sentenceId = $model->id;
        $model->Transcription->deleteAll(
            array('Transcription.sentence_id' => $sentenceId),
            false,
            false
        );
    }

    public function afterFind(&$model, $results, $primary) {
        foreach ($results as &$result) {
            if (isset($result['Transcription'])
                && isset($result[$model->alias]['lang'])
                && isset($result[$model->alias]['text'])) {
                $result['Transcription'] =
                    $model->Transcription->addGeneratedTranscriptions(
                        $result['Transcription'], $result[$model->alias]
                    );
            }
        }
        return $results;
    }
}
