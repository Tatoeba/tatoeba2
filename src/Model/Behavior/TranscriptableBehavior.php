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

use Cake\Model\Behavior;
use Cake\Model\Model;


/**
 * Model behavior for transcriptions/transliterations.
 * Only attached to the Sentence model.
 */
class TranscriptableBehavior extends ModelBehavior
{
    private function addScriptInformation($model) {
        $lang = $model->_getFieldFromDataOrDatabase('lang');
        $text = $model->_getFieldFromDataOrDatabase('text');
        $script = $model->Transcription->detectScript($lang, $text);
        $model->data[$model->alias]['script'] = $script;
    }

    public function beforeSave(Model $model, $options = array()) {
        $isNewSentence = !$model->id;
        if ($isNewSentence) {
            if (!isset($model->data[$model->alias]['script']))
                $this->addScriptInformation($model);
        } else {
            if (isset($model->data[$model->alias]['text']) ||
                isset($model->data[$model->alias]['lang']))
                $this->addScriptInformation($model);
        }
        if (!$this->isScriptValid($model)) {
            return false;
        }
        return true;
    }

    private function isScriptValid($model) {
        if (!isset($model->data[$model->alias]['script']))
            return true;

        $script = $model->data[$model->alias]['script'];
        $lang = $model->_getFieldFromDataOrDatabase('lang');
        return $model->Transcription->isValidScriptForLanguage($lang, $script);
    }

    public function afterSave(Model $model, $created, $options = array()) {
        if ($created) {
            $model->data[$model->alias]['id'] = $model->getLastInsertID();
            $this->createTranscriptions($model, $model->data);
        } else {
            if (array_key_exists('text', $model->data[$model->alias]) ||
                array_key_exists('lang', $model->data[$model->alias])) {
                $this->deleteTranscriptions($model);
                $sentence = $model->find('first', array(
                    'conditions' => array(
                        $model->alias . '.' . $model->primaryKey => $model->id
                    ),
                ));
                $this->createTranscriptions($model, $sentence);
            }
        }
    }

    private function createTranscriptions($model, $data = null) {
        $sentence = $data[$model->alias];
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
