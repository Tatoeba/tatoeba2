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

App::uses('AppController', 'Controller');

class TranscriptionsController extends AppController
{
    public $name = 'Transcriptions';

    public $uses = array(
        'Transcription',
        'Sentence',
        'User',
    );

    public $components = array(
    );

    public $helpers = array(
        'Pagination',
        'Transcriptions',
    );

    public $paginate = array(
        'contain' => array('User', 'Sentence'),
        'limit' => 100,
        'order' => 'Transcription.modified DESC'
    );

    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->Auth->allowedActions = array(
            'view',
            'of',
        );

        $this->Security->unlockedActions = array(
            'save',
            'reset'
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
        $transcr = $this->Transcription->findTranscription($sentenceId, $script);
        $transcrOwnerId = $transcr ? $transcr['Transcription']['user_id'] : null;
        $sentence = $this->Sentence->findById($sentenceId);
        $sentenceOwnerId = $sentence ? $sentence['Sentence']['user_id'] : null;
        $canEdit = CurrentUser::canEditTranscription(
            $transcrOwnerId, $sentenceOwnerId
        );

        $saved = false;
        if ($canEdit) {
            if ($transcr) {
                $this->Transcription->delete($transcr['Transcription']['id'], false);
            }
            $saved = $this->Transcription->generateTranscription(
                $sentence,
                $script,
                true
            );
        }

        if (!$saved) {
            $this->header('HTTP/1.1 400 Bad transcription');
        }

        $this->setViewVars(array_filter(array($saved)), $sentenceId, $sentence);
        $this->render('view');
    }

    public function save($sentenceId, $script) {
        $transcr = $this->Transcription->findTranscription($sentenceId, $script);
        $transcrOwnerId = $transcr ? $transcr['Transcription']['user_id'] : null;
        $sentence = $this->Sentence->findById($sentenceId, 'user_id');
        $sentenceOwnerId = $sentence ? $sentence['Sentence']['user_id'] : null;
        $canEdit = CurrentUser::canEditTranscription(
            $transcrOwnerId, $sentenceOwnerId
        );

        $saved = false;
        if ($canEdit) {
            $data = array(
                'sentence_id' => $sentenceId,
                'script' => $script,
                'text' => $this->request->data['value'],
                'user_id' => CurrentUser::get('id'),
            );
            if ($transcr) { // Modifying existing transcription
                $data['id'] = $transcr['Transcription']['id'];
            }
            $saved = $this->Transcription->saveTranscription($data);
        }

        if (!$saved) {
            $this->header('HTTP/1.1 400 Bad transcription');
        } else {
            $saved['User'] = array(
                'username' => CurrentUser::get('username')
            );
        }

        /* Used by tests, to check permissions */
        if (isset($this->request->params['requested'])) {
            return $canEdit && $saved;
        }

        $this->setViewVars(array_filter(array($saved)), $sentenceId);
        $this->render('view');
    }

    public function of($username) {
        $userId = $this->User->getIdFromUsername($username);
        if ($userId) {
            $result = $this->paginate('Transcription', array(
                'Transcription.user_id' => $userId
            ));
            $sentencesWithTranscription = array();
            foreach ($result as $data) {
                $sentenceId = $data['Sentence']['id'];
                if (!isset($sentencesWithTranscription[$sentenceId])) {
                    $sentencesWithTranscription[$sentenceId] = array(
                        'Sentence' => $data['Sentence'],
                        'Transcription' => array()
                    );
                }
                $data['Transcription']['User'] = $data['User'];
                $sentencesWithTranscription[$sentenceId]['Transcription'][] =
                    $data['Transcription'];
            }
            $this->set(compact('sentencesWithTranscription'));
        }
        $this->set(compact('username'));
    }

    private function setViewVars($transcriptions, $sentenceId, $sentence = null) {
        if ($transcriptions) {
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

        $this->set('transcr', $transcriptions);
        $this->set('validationErrors', $this->Transcription->validationErrors);
        $this->layout = null;
    }
}
