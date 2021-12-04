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
        'contain' => [
            'Users' => ['fields' => ['username']],
            'Sentences' => ['Transcriptions']
        ],
        'limit' => 100,
        'order' => ['Audios.modified' => 'DESC']
    ];

    public function beforeFilter(Event $event)
    {

        return parent::beforeFilter($event);
    }

    public function import() {
        $this->loadModel('Audios');
        $filesImported = $errors = false;
        if ($this->request->is('post')) {
            $author = $this->request->getData('audioAuthor');
            $filesImported = $this->Audios->importFiles($errors, $author);
        }
        $filesToImport = $this->Audios->getFilesToImport();

        $this->set(compact('filesToImport', 'errors', 'filesImported'));
    }

    public function index($lang = null) {
        $conditions = array();
        if (LanguagesLib::languageExists($lang)) {
            $conditions['Sentences.lang'] = $lang;
            $this->set(compact('lang'));
        }
        $this->paginate['conditions'] = $conditions;
        $sentencesWithAudio = $this->paginate('Audios');
        
        $this->loadModel('Languages');
        $this->set(compact('sentencesWithAudio'));
        $this->set(array('stats' => $this->Languages->getAudioStats()));
        $this->set('lang', $lang);
    }

    public function of($username) {
        $this->loadModel('Users');
        $userId = $this->Users->getIdFromUsername($username);
        if ($userId) {
            $this->paginate['conditions'] = [
                'Audios.user_id' => $userId,
            ];
            $sentencesWithAudio = $this->paginate('Audios');
            $this->set(compact('sentencesWithAudio'));

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
        $this->loadModel('Audios');
        try {
            $audio = $this->Audios->get($id);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            throw new \Cake\Http\Exception\NotFoundException();
        }

        return $this->getResponse()
                    ->withFile($audio->file_path, ['download' => true]);
    }
}
