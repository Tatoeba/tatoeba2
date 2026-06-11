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

    public $paginate = [
        'limit' => 100,
    ];

    protected $defaultTable = 'Audios';

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        $this->Security->setConfig('unlockedActions', [
            'save',
            'delete',
        ]);

        return parent::beforeFilter($event);
    }

    public function import() {
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

        $filesImported = $errors = [];
        $filesToImport = $this->Audios->getFilesToImport();
        if ($lastImportJob && is_string($lastImportJob->failure_message)) {
            $result = unserialize($lastImportJob->failure_message);
            if ($result) {
                $filesImported = $result['filesImported'];
                $errors = $result['errors'];
            }
        }
        $this->set(compact('lastImportJob', 'filesToImport', 'filesImported', 'errors'));
    }

    public function index($lang = null) {
        $totalLimit = $this::PAGINATION_DEFAULT_TOTAL_LIMIT;
        $finder = ['sentences' => [
            'maxResults' => $totalLimit,
            'sentences' => [],
        ]];
        if (LanguagesLib::languageExists($lang)) {
            $finder['sentences']['lang'] = $lang;
            $this->set(compact('lang'));
        }
        $total = $this->Audios->find('sentencesCounter', $finder['sentences'])->count();

        try {
            $sentencesWithAudio = $this->paginate($this->Audios, compact('finder'));
        } catch (\Cake\Http\Exception\NotFoundException $e) {
            return $this->redirectPaginationToLastPage();
        }

        $this->set(compact('sentencesWithAudio', 'totalLimit', 'total'));
        
        $this->set(array('stats' => $this->fetchTable('Languages')->getAudioStats()));
    }

    public function of($username) {
        $Users = $this->fetchTable('Users');
        $userId = $Users->getIdFromUsername($username);
        if ($userId) {
            $totalLimit = $this::PAGINATION_DEFAULT_TOTAL_LIMIT;
            $finder = ['sentences' => [
                'user_id' => $userId,
                'maxResults' => $totalLimit,
            ]];
            try {
                $sentencesWithAudio = $this->paginate($this->Audios, compact('finder'));
            } catch (\Cake\Http\Exception\NotFoundException $e) {
                return $this->redirectPaginationToLastPage();
            }
            $this->set(compact('sentencesWithAudio'));

            $this->set('totalAudio', $this->Audios->numberOfAudiosBy($userId));

            $audioSettings = $Users->getAudioSettings($userId);
            $this->set(compact('audioSettings', 'totalLimit'));
        }
        $this->set(compact('username'));
    }

    public function save_settings() {
        if (!empty($this->request->getData())) {
            $currentUserId = CurrentUser::get('id');
            $allowedFields = array(
                'audio_license',
                'audio_attribution_url',
            );
            $dataToSave = $this->filterKeys($this->request->getData(), $allowedFields);
            $Users = $this->fetchTable('Users');
            $user = $Users->get($currentUserId);
            $Users->patchEntity($user, $dataToSave);
            if ($Users->save($user)) {
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

        try {
            $audio = $this->Audios->get($id);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            if (CurrentUser::isAdmin()) {
                try {
                    $audio = $this->fetchTable('DisabledAudios')->get($id);
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
        $this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('post')) {
            $audio = false;
            try {
                $audio = $this->Audios->get($id);
            } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                if (CurrentUser::isAdmin()) {
                    try {
                        $audio = $this->fetchTable('DisabledAudios')->get($id);
                    } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                    }
                }
            }

            if ($audio) {
                $body = (string)$this->request->getBody();
                $fields = json_decode($body, true);
                $sourceTable = $this->fetchTable($audio->getSource());
                $sourceTable->edit($audio, $fields);
                if ($sourceTable->save($audio)) {
                    return $this->response->withStringBody(''); // OK
                }
            }
            throw new \Cake\Http\Exception\NotFoundException();
        }

        throw new \Cake\Http\Exception\BadRequestException();
    }

    public function delete($id) {
        $this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('post')) {
            $audio = false;
            try {
                $audio = $this->Audios->get($id);
            } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                if (CurrentUser::isAdmin()) {
                    try {
                        $audio = $this->fetchTable('DisabledAudios')->get($id);
                    } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                    }
                }
            }

            if ($audio) {
                $sourceTable = $this->fetchTable($audio->getSource());
                if ($sourceTable->delete($audio, ['deleteAudioFile' => true])) {
                    return $this->response->withStringBody(''); // OK
                }
            }
            throw new \Cake\Http\Exception\NotFoundException();
        }

        throw new \Cake\Http\Exception\BadRequestException();
    }
}
