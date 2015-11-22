<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2014 Gilles Bedel
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

class TranscriptionsController extends AppController
{
    public $name = 'Transcriptions';

    public $uses = array(
        'Transcription',
        'Sentence',
    );

    public $components = array(
    );

    public $helpers = array(
        'Transcriptions',
    );

    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->Auth->allowedActions = array(
            'view',
        );
    }

    public function view($sentenceId) {
        $transcr = $this->Transcription->find('all', array(
            'conditions' => array('sentence_id' => $sentenceId),
        ));
        $transcr = Set::classicExtract($transcr, '{n}.Transcription');
        $this->setViewVars($transcr, $sentenceId);
    }

    public function reset($sentenceId, $script) {
        $transcriptionId = $this->Transcription->findTranscriptionId($sentenceId, $script);
        $saved = false;
        $sentence = null;

        if ($transcriptionId) {
            list($transcrOwnerId, $sentenceOwnerId)
                = $this->Transcription->getOwners($transcriptionId);
            $canEdit = CurrentUser::canEditTranscription(
                $transcrOwnerId, $sentenceOwnerId
            );
            if ($canEdit) {
                $this->Transcription->delete($transcriptionId, false);
                $sentence = $this->Sentence->findById($sentenceId);
                $saved = $this->Transcription->generateTranscription(
                    $sentence,
                    $script,
                    true
                );
            }
        } else {
            $sentence = $this->Sentence->findById($sentenceId);
            $saved = $this->Transcription->generateTranscription(
                $sentence,
                $script,
                true
            );
        }

        if (!$saved) {
            $this->header('HTTP/1.1 400 Bad transcription');
        }

        $this->setViewVars($saved, $sentenceId, $sentence);
        $this->render('view');
    }

    public function save($sentenceId, $script) {
        $transcriptionId = $this->Transcription->findTranscriptionId($sentenceId, $script);
        $transcriptionText = $this->params['form']['value'];
        $userId = CurrentUser::get('id');
        $canEdit = true;

        if ($transcriptionId) { // Modifying existing transcription
            list($transcrOwnerId, $sentenceOwnerId)
                = $this->Transcription->getOwners($transcriptionId);
            $canEdit = CurrentUser::canEditTranscription(
                $transcrOwnerId, $sentenceOwnerId
            );

            $saved = false;
            if ($canEdit) {
                $saved = $this->Transcription->saveTranscription(array(
                    'id' => $transcriptionId,
                    'sentence_id' => $sentenceId,
                    'script' => $script,
                    'text' => $transcriptionText,
                    'user_id' => $userId,
                ));
            }
        } else { // Inserting a new transcription
            $saved = $this->Transcription->saveTranscription(array(
                'text' => $transcriptionText,
                'sentence_id' => $sentenceId,
                'script' => $script,
                'user_id' => $userId,
            ));
        }

        if (!$saved) {
            $this->header('HTTP/1.1 400 Bad transcription');
        }

        /* Used by tests, to check permissions */
        if (isset($this->params['requested'])) {
            return $canEdit && $saved;
        }

        $this->setViewVars($saved, $sentenceId);
        $this->render('view');
    }

    private function setViewVars($transcription, $sentenceId, $sentence = null) {
        if ($transcription) {
            if (!$sentence) {
                $sentence = $this->Sentence->findById(
                    $sentenceId,
                    array('lang', 'user_id')
                );
            }
            if ($sentence) {
                $this->set('lang', $sentence['Sentence']['lang']);
                $this->set('sentenceOwnerId', $sentence['Sentence']['user_id']);
            }
        }

        $this->set('transcr', $transcription);
        $this->set('validationErrors', $this->Transcription->validationErrors);
        $this->layout = null;
    }
}
?>
