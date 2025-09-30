<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2016  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use App\Model\CurrentUser;
use Cake\Datasource\Exception\RecordNotFoundException;

/**
 * Controller for vocabulary.
 *
 * @category Vocabulary
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class VocabularyController extends AppController
{
    public $uses = array('UsersVocabulary', 'User', 'Sentence', 'Vocabulary');
    public $components = array ('CommonSentence', 'Flash');
    public $helpers = array(
        'Vocabulary',
    );

    /**
     * Before filter.
     *
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        $this->Security->config('unlockedActions', [
            'save', 'save_sentence', 'edit'
        ]);
        return parent::beforeFilter($event);
    }


    /**
     * Page that lists all the vocabulary items of given user in given language.
     *
     * @param $username string Username of the user.
     * @param $lang     string Language of the items.
     */
    public function of($username, $lang = null)
    {
        $this->helpers[] = 'Pagination';
        $this->helpers[] = 'CommonModules';

        $this->loadModel('Users');
        $userId = $this->Users->getIdFromUsername($username);

        if (!$userId) {
            $this->Flash->set(format(
                __('No user with this username: {username}'),
                array('username' => $username)
            ));
            $this->redirect(
                array('controller'=>'users',
                  'action' => 'all')
            );
        }
        $this->loadModel('UsersVocabulary');
        $this->paginate = $this->UsersVocabulary->getPaginatedVocabularyOf(
            $userId,
            $lang
        );
        $results = $this->paginate('UsersVocabulary');

        $vocabulary = $this->Vocabulary->syncNumSentences($results);

        $this->set('vocabulary', $vocabulary);
        $this->set('username', $username);
        $this->set('canEdit', $username == CurrentUser::get('username'));
    }


    /**
     * Page where users can add new vocabulary items to their list.
     */
    public function add()
    {
    }

    public function edit($id)
    {
        $this->autoRender = false;

        $this->loadModel('UsersVocabulary');
        $usersVocab = $this->UsersVocabulary
            ->find()
            ->where(['vocabulary_id' => (int)$id])
            ->contain('Vocabulary')
            ->all();

        if ($usersVocab->count() == 0) {
            return $this->response->withStatus(404);
        }

        $canEdit = $usersVocab->count() == 1 && $usersVocab->first()->user_id == CurrentUser::get('id');
        if (!$canEdit) {
            return $this->response->withStatus(403);
        }

        $vocab = $usersVocab->first()->vocabulary;
        $this->Vocabulary->patchEntity(
            $vocab,
            $this->request->getData(),
            ['fields' => ['lang', 'text']]
        );

        if (!$this->Vocabulary->save($vocab)) {
            return $this->response->withStatus(400);
        }
    }

    /**
     * Page that lists all the vocabulary items for which sentences are wanted.
     *
     * @param $lang string Language of the vocabulary items.
     */
    public function add_sentences($lang = null)
    {   
        $this->request->getSession()->write('vocabulary_requests_filtered_lang', $lang);

        $this->helpers[] = 'Pagination';
        $this->helpers[] = 'CommonModules';
        $this->helpers[] = 'Languages';

        $this->paginate = $this->Vocabulary->getPaginatedVocabulary($lang);
        $vocabulary = $this->paginate('Vocabulary');

        $this->set('vocabulary', $vocabulary);
        $this->set('langFilter', $lang);
    }


    /**
     * Save a vocabulary item.
     */
    public function save()
    {
        $lang = $this->request->getData('lang');
        $text = $this->request->getData('text');

        $result = $this->Vocabulary->addItem($lang, $text);

        $this->set('result', $result);
        $this->viewBuilder()->setLayout('json');
    }


    /**
     * Removes vocabulary item of given id.
     *
     * @param $vocabularyId int Vocabulary item id.
     */
    public function remove($vocabularyId)
    {
        $this->loadModel('UsersVocabulary');
        $data = $this->UsersVocabulary->findFirst(
            $vocabularyId,
            CurrentUser::get('id')
        );

        if ($data) {
            $this->UsersVocabulary->delete($data);
        }

        $this->set('vocabularyId', array('id' => $vocabularyId, 'data' => $data));

        $this->viewBuilder()->setLayout('json');
    }


    /**
     * Saves a sentence for vocabulary of given id and updates the count of
     * sentences for that vocabulary item.
     *
     * @param int $vocabularyId Hexadecimal value of the vocabulary id.
     */
    public function save_sentence($vocabularyId)
    {
        $sentenceLang = $this->request->getData('lang');
        $sentenceText = $this->request->getData('text');
        $userId = CurrentUser::get('id');
        $username = CurrentUser::get('username');

        $savedSentence = $this->CommonSentence->addNewSentence(
            $sentenceLang,
            $sentenceText,
            $userId,
            $username
        );

        if (!$savedSentence->isDuplicate) {
            $numSentences = $this->Vocabulary->incrementNumSentences(
                $vocabularyId,
                $sentenceText
            );
        }

        $sentence = [
            'id' => $savedSentence->id,
            'text' => $sentenceText,
            'duplicate' => $savedSentence->isDuplicate
        ];

        $this->set('sentence', $sentence);

        $this->viewBuilder()->setLayout('json');
    }
}
