<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2016 Gilles Bedel
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
use Cake\Event\Event;
use App\Lib\LanguagesLib;
use App\Model\CurrentUser;

class AudioController extends AppController
{
    public $name = 'Audio';

    public $uses = array(
        'Audio',
        'Language',
        'User',
        'CurrentUser'
    );

    public $components = array(
        'Flash'
    );

    public $helpers = array(
        'Pagination',
        'Languages',
        'Audio',
    );

    public $paginate = [
        'limit' => 100,
    ];

    public function beforeFilter(Event $event)
    {
        $this->Security->config('unlockedActions', [
            'save',
            'delete',
        ]);

        return parent::beforeFilter($event);
    }

    public function import() {
        $this->loadModel('Audios');
        $lastImportJob = $this->Audios->lastImportJob();
        $canImport = !$lastImportJob || $lastImportJob->completed;

        if ($canImport && $this->request->is('post')) {
            $author = $this->request->getData('audioAuthor');
            $replace = array_filter($this->request->getData('replace', []));
            $this->Audios->enqueueImportTask($author, $replace);
            // redirect to same URL so that refreshing the page
            // results in a GET request, not a duplicate POST
            return $this->redirect([]);
        }

        $filesToImport = $this->Audios->getFilesToImport();
        if ($lastImportJob) {
            $result = unserialize($lastImportJob->failure_message);
            $filesImported = $result['filesImported'];
            $errors = $result['errors'];
        } else {
            $filesImported = $errors = [];
        }
        $this->set(compact('lastImportJob', 'filesToImport', 'filesImported', 'errors'));
    }

    public function index($lang = null) {
        $this->loadModel('Audios');

        $totalLimit = $this::PAGINATION_DEFAULT_TOTAL_LIMIT;
        $finder = ['sentences' => [
            'maxResults' => $totalLimit,
        ]];
        $total = $this->Audios->find()->select(['sentence_id'])->distinct();
        if (LanguagesLib::languageExists($lang)) {
            $total = $total->where(['sentence_lang' => $lang]);
            $finder['sentences']['lang'] = $lang;
            $this->set(compact('lang'));
        }
        $total = $total->count();

        try {
            $sentencesWithAudio = $this->paginate($this->Audios, compact('finder'));
        } catch (\Cake\Http\Exception\NotFoundException $e) {
            return $this->redirectPaginationToLastPage();
        }

        $this->set(compact('sentencesWithAudio', 'totalLimit', 'total'));
        
        $this->loadModel('Languages');
        $this->set(array('stats' => $this->Languages->getAudioStats()));
    }

    public function of($username) {
        $this->loadModel('Users');
        $userId = $this->Users->getIdFromUsername($username);
        if ($userId) {
            $this->loadModel('Audios');

            $finder = ['sentences' => ['user_id' => $userId]];
            $sentencesWithAudio = $this->paginate($this->Audios, compact('finder'));
            $this->set(compact('sentencesWithAudio'));

            $this->set('totalAudio', $this->Audios->numberOfAudiosBy($userId));

            $audioSettings = $this->Users->getAudioSettings($userId);
            $this->set(compact('audioSettings'));
        }
        $this->set(compact('username'));
    }

    public function save_settings() {
        if (!empty($this->request->data)) {
            $currentUserId = CurrentUser::get('id');
            $allowedFields = array(
                'audio_license',
                'audio_attribution_url',
            );
            $dataToSave = $this->filterKeys($this->request->data, $allowedFields);
            $this->loadModel('Users');
            $user = $this->Users->get($currentUserId);
            $this->Users->patchEntity($user, $dataToSave);
            if ($this->Users->save($user)) {
                $flashMsg = __('Your audio settings have been saved.');
            } else {
                $flashMsg = __(
                    'An error occurred while saving. Please try again or '.
                    'contact us to report this.',
                    true
                );
            }
            $this->Flash->set($flashMsg);
        }

        $this->redirect(array('action' => 'of', CurrentUser::get('username')));
    }

    public function download($id) {
        $audio = false;

        $this->loadModel('Audios');
        try {
            $audio = $this->Audios->get($id);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            if (CurrentUser::isAdmin()) {
                $this->loadModel('DisabledAudios');
                try {
                    $audio = $this->DisabledAudios->get($id);
                } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                }
            }
        }

        if ($audio) {
            $options = [
                'download' => true,
                'name' => $audio->pretty_filename,
            ];
            return $this->getResponse()
                        ->withFile($audio->file_path, $options);
        } else {
            throw new \Cake\Http\Exception\NotFoundException();
        }
    }

    public function save($id) {
        $this->viewBuilder()->autoLayout(false);

        if ($this->request->is('post')) {
            $audio = false;
            $this->loadModel('Audios');
            try {
                $audio = $this->Audios->get($id);
            } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                if (CurrentUser::isAdmin()) {
                    $this->loadModel('DisabledAudios');
                    try {
                        $audio = $this->DisabledAudios->get($id);
                    } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                    }
                }
            }

            if ($audio) {
                $fields = $this->request->input('json_decode', true);
                $source = $audio->getSource();
                $this->{$source}->edit($audio, $fields);
                if ($this->{$source}->save($audio)) {
                    return $this->response->withStringBody(''); // OK
                }
            }
            throw new \Cake\Http\Exception\NotFoundException();
        }

        throw new \Cake\Http\Exception\BadRequestException();
    }

    public function delete($id) {
        $this->viewBuilder()->autoLayout(false);

        if ($this->request->is('post')) {
            $audio = false;
            $this->loadModel('Audios');
            try {
                $audio = $this->Audios->get($id);
            } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                if (CurrentUser::isAdmin()) {
                    $this->loadModel('DisabledAudios');
                    try {
                        $audio = $this->DisabledAudios->get($id);
                    } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                    }
                }
            }

            if ($audio) {
                $source = $audio->getSource();
                if ($this->{$source}->delete($audio, ['deleteAudioFile' => true])) {
                    return $this->response->withStringBody(''); // OK
                }
            }
            throw new \Cake\Http\Exception\NotFoundException();
        }

        throw new \Cake\Http\Exception\BadRequestException();
    }
}
