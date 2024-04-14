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
namespace App\Controller;

use App\Controller\AppController;
use App\Model\CurrentUser;
use Cake\Event\Event;

class TranscriptionsController extends AppController
{
    public $name = 'Transcriptions';

    public $components = array(
    );

    public $helpers = array(
        'Pagination',
        'Transcriptions',
    );

    public $paginate = array(
        'contain' => array('Users', 'Sentences'),
        'limit' => 100,
        'order' => array('Transcriptions.modified' => 'desc'),
    );

    public function beforeFilter(Event $event)
    {
        $this->Security->config('unlockedActions', [
            'save',
            'reset'
        ]);

        return parent::beforeFilter($event);
    }

    public function view($sentenceId) {
        $transcr = $this->Transcriptions->find()
            ->where(['sentence_id' => $sentenceId])
            ->toList();
        $this->setViewVars($transcr, $sentenceId);
    }

    public function reset($sentenceId, $script) {
        $transcr = $this->Transcriptions->findTranscription($sentenceId, $script);
        $transcrOwnerId = null;
        if ($transcr) {
            $transcrOwnerId = $transcr->user_id;
            $sentence = $transcr->sentence;
        } else {
            $this->loadModel('Sentences');
            $sentence = $this->Sentences->get($sentenceId);
        }
        $sentenceOwnerId = $sentence ? $sentence->user_id : null;
        $canEdit = CurrentUser::canEditTranscription(
            $transcrOwnerId, $sentenceOwnerId
        );

        $saved = false;
        if ($canEdit) {
            if ($transcr) {
                $this->Transcriptions->delete($transcr);
            }
            $saved = $this->Transcriptions->generateTranscription(
                $sentence,
                $script,
                true
            );
        }

        $acceptsJson = $this->request->accepts('application/json');
        if ($acceptsJson) {
            return $this->returnTranscriptionData($saved, $sentenceId, $script);
        } else {
            if (!$saved) {
                return $this->response->withStatus(400, 'Bad transcription');
            }
            $this->setViewVars(array_filter(array($saved)), $sentenceId, $sentence);
            $this->render('view');
        }
    }

    public function save($sentenceId, $script) {
        $transcr = $this->Transcriptions->findTranscription($sentenceId, $script);
        $transcrOwnerId = null;
        if ($transcr) {
            $transcrOwnerId = $transcr->user_id;
            $sentence = $transcr->sentence;
        } else {
            $this->loadModel('Sentences');
            $sentence = $this->Sentences->get($sentenceId);
        }
        $sentenceOwnerId = $sentence ? $sentence->user_id : null;
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
                $data['id'] = $transcr->id;
            }
            $saved = $this->Transcriptions->saveTranscription($data);
        }

        if ($saved) {
            $saved['user'] = array(
                'username' => CurrentUser::get('username')
            );
        }

        $acceptsJson = $this->request->accepts('application/json');
        if ($acceptsJson) {
            return $this->returnTranscriptionData($saved, $sentenceId, $script);
        } else {
            $this->setViewVars(array_filter(array($saved)), $sentenceId);
            $this->render('view');
            if (!$saved) {
                return $this->response->withStatus(400, 'Bad transcription');
            }
        }
    }

    private function returnTranscriptionData($saved, $sentenceId, $script) {
        if ($saved) {
            $transcription = $this->Transcriptions->findTranscription($sentenceId, $script);
            $this->set('result', $transcription);
            $this->loadComponent('RequestHandler');
            $this->set('_serialize', ['result']);
            $this->RequestHandler->renderAs($this, 'json');    
        } else {
            $errors = json_encode($this->Transcriptions->validationErrors);
            return $this->response->withStatus(400)->withStringBody($errors);
        }
    }

    public function of($username) {
        $this->loadModel('Users');
        $userId = $this->Users->getIdFromUsername($username);
        if ($userId) {
            $query = $this->Transcriptions
                ->find()
                ->where(['Transcriptions.user_id' => $userId])
                ->mapReduce(
                    function ($transcr, $key, $mapReduce) {
                        $mapReduce->emitIntermediate($transcr, $transcr->sentence_id);
                    },
                    function ($transcrs, $sentenceId, $mapReduce) {
                        $sentence = $transcrs[0]->sentence;
                        $sentence->transcriptions = $transcrs;
                        $mapReduce->emit($sentence, $sentenceId);
                    }
                );
            $results = $this->paginate($query);
            $this->set('results', $results);
        }
        $this->set('userId', $userId);
        $this->set('username', $username);
    }

    private function setViewVars($transcriptions, $sentenceId, $sentence = null) {
        if ($transcriptions) {
            if (!$sentence) {
                $this->loadModel('Sentences');
                $sentence = $this->Sentences->get(
                    $sentenceId,
                    ['fields' => ['lang', 'user_id']]
                );
            }
            if ($sentence) {
                $this->set('lang', $sentence->lang);
                $this->set('sentenceOwnerId', $sentence->user_id);
            }
        }

        $this->set('transcr', $transcriptions);
        $this->set('validationErrors', $this->Transcriptions->validationErrors);
        $this->viewBuilder()->autoLayout(false);
    }
}
