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
        'Sentences',
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

    public function save($sentenceId, $script) {
        $transcriptionId = $this->Transcription->findTranscriptionId($sentenceId, $script);
        $transcriptionText = $this->params['form']['value'];
        $userId = CurrentUser::get('id');

        if ($transcriptionId) { // Modifying existing transcription
            list($transcrOwnerId, $sentenceOwnerId)
                = $this->Transcription->getOwners($transcriptionId);
            $canEdit = (
                $transcrOwnerId === null
                || $sentenceOwnerId === $userId
                || $transcrOwnerId === $userId
                || CurrentUser::isModerator()
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

        /* Used by tests, to check permissions */
        if (isset($this->params['requested'])) {
            return $canEdit && $saved;
        }

        $this->setViewVars($saved, $sentenceId);
        $this->render('view');
    }

    private function setViewVars($transcription, $sentenceId) {
        $sentence = $this->Sentence->findById($sentenceId, 'lang');
        if ($sentence)
            $this->set('lang', $sentence['Sentence']['lang']);

        $this->set('transcr', $transcription);
        $this->layout = null;
    }
}
?>
