<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009-2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 * @link     http://tatoeba.org
 */
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use App\Model\CurrentUser;
use App\Model\Entity\SentencesList;

/**
 * Controller for sentences lists.
 *
 * @category SentencesLists
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class SentencesListsController extends AppController
{
    public $name = 'SentencesLists';
    public $helpers = array(
        'Sentences',
        'Navigation',
        'Csv',
        'CommonModules',
        'Html',
        'Lists',
        'Menu',
        'Pagination'
    );
    public $components = array(
        'Flash',
        'LanguageDetection',
        'CommonSentence'
    );

    public $uses = array('SentencesList', 'SentencesSentencesLists', 'User');

    public function initialize() {
        parent::initialize();
        $params = $this->request->params;
        $noCsrfActions = [
            'set_option',
            'save_name',
            'add_new_sentence_to_list'
        ];
        if (in_array($params['action'], $noCsrfActions)) {
            $this->components()->unload('Csrf');
        }
    }


    /**
     * Before filter.
     *
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        $this->Security->config('unlockedActions', [
            'set_option',
            'save_name',
            'add_new_sentence_to_list'
        ]);

        return parent::beforeFilter($event);
    }

    /**
     * Displays all the lists. If user is logged in, it will also display a form to
     * add a new list and the lists that belong to that user.
     *
     * @param string $filter
     */
    public function index($filter = null)
    {
        $search = $this->request->getQuery('search');
        if (!is_null($search)) {
            return $this->redirect(array('action' => 'index', $search));
        }

        $this->paginate = $this->SentencesLists->getPaginatedLists(
            $filter, null, 'public'
        );
        $allLists = $this->paginate();

        $this->set('allLists', $allLists);
        $this->set('filter', $filter);
    }


    public function collaborative($filter = null)
    {
        $search = $this->request->getQuery('search');
        if (!is_null($search)) {
            return $this->redirect(array('action' => 'collaborative', $search));
        }

        $this->paginate = $this->SentencesLists->getPaginatedLists(
            $filter, null, 'public', 'anyone'
        );
        $allLists = $this->paginate();

        $this->set('allLists', $allLists);
        $this->set('filter', $filter);
        $this->set('isCollaborative', true);

        $this->render('index');
    }


    /**
     * Display content of a list.
     *
     * @param int    $id               Id of list.
     * @param string $translationsLang Language of translations.
     *
     * @return mixed
     */
    public function show($id = null, $translationsLang = null)
    {
        if (empty($translationsLang)) {
            $translationsLang = 'none';
        }

        if (!isset($id)) {
            return $this->redirect(array('action' => 'index'));
        }

        $list = $this->SentencesLists->getListWithPermissions(
            $id, CurrentUser::get('id')
        );

        if (!$list['Permissions']['canView']) {
            $this->Flash->set(
                __('You do not have permission to view this list.')
            );
            return $this->redirect(array('action' => 'index'));
        }

        $this->loadModel('Sentences');
        
        $contain = $this->Sentences->paginateContain();
        $contain['finder'] = ['filteredTranslations' => [
            'translationLang' => $translationsLang
        ]];
        $pagination = [
            'contain' => ['Sentences' => $contain],
            'conditions' => ['sentences_list_id' => $id],
            'limit' => CurrentUser::getSetting('sentences_per_page'),
            'order' => ['SentencesSentencesLists.created' => 'DESC']
        ];
        $this->paginate = $pagination;
        $sentencesInList = $this->paginate('SentencesSentencesLists');

        $this->set('translationsLang', $translationsLang);
        $this->set('list', $list);
        $this->set('user', $list->user);
        $this->set('permissions', $list['Permissions']);
        $this->set('sentencesInList', $sentencesInList);
    }


    /**
     * Create a list.
     *
     * @return void
     */
    public function add()
    {
        $list = $this->SentencesLists->createList(
            $this->request->getData('name'), 
            $this->Auth->user('id')
        );
        
        if (isset($list->id)) {
            return $this->redirect(array('action' => 'show', $list['id']));
        } else {
            return $this->redirect(array('action' => 'index'));
        }
    }


    /**
     * Saves the new name of a list.
     * Used in AJAX request from sentences_lists.edit_name.js
     *
     * @return void
     */
    public function save_name()
    {
        $userId = $this->Auth->user('id');
        $listId = substr($this->request->getData('id'), 1);
        $listName = $this->request->getData('value');
        
        if ($this->SentencesLists->editName($listId, $listName, $userId)) {
            $this->set('result', $listName);
        } else {
            $this->set('result', 'error');
        }
    }


    /**
     * Delete list.
     *
     * @param int $listId Id of list.
     *
     * @return void
     */
    public function delete($listId)
    {
        $userId = $this->Auth->user('id');
        if ($this->SentencesLists->deleteList($listId, $userId)) {
            // Retrieve the 'most_recent_list' cookie, and if it matches
            // $listId, erase it. Do this even if the 'remember_list' has
            // not been set, or has been set to false.
            $mostRecentList = $this->request->getSession()->read('most_recent_list');
            if ($mostRecentList == $listId)
            {
                $this->request->getSession()->delete('most_recent_list');
            }
        }
        
        return $this->redirect(array('action' => 'index'));
    }

    /**
     * Add sentence to a list.
     *
     * @param int $sentenceId Id of sentence to add.
     * @param int $listId     Id of list to which to add the sentence.
     *
     * @return void
     */
    public function add_sentence_to_list($sentenceId, $listId)
    {
        $userId = $this->Auth->user('id');
        if ($this->SentencesLists->addSentenceToList($sentenceId, $listId, $userId)) {
            $this->set('result', $listId);
            $this->Cookie->write('most_recent_list', $listId, false, "+1 month");
        } else {
            $this->set('result', 'error');
        }
    }


    /**
     * Remove sentence from a list.
     *
     * @param int $sentenceId Id of sentence to be removed from list.
     * @param int $listId     Id of list that contains the sentence.
     *
     * @return void
     */
    public function remove_sentence_from_list($sentenceId, $listId)
    {
        $userId = $this->Auth->user('id');
        $isRemoved = $this->SentencesLists->removeSentenceFromList(
            $sentenceId, $listId, $userId
        );
        $this->set('removed', $isRemoved);
    }


    /**
     * Displays the lists of a specific user.
     *
     * @param string $username Username of of the user we want lists of.
     * @param string $filter   Search query on name of list.
     */
    public function of_user($username=null, $filter = null)
    {
        $usernameParam = $this->request->getQuery('username');
        $searchParam   = $this->request->getQuery('search');

        if (!is_null($usernameParam)) {
            return $this->redirect(array('action' => 'of_user', $usernameParam, $searchParam));
        } else if (empty($username)) {
            return $this->redirect(array('action' => 'index'));
        }

        $this->set('username', $username);
        $this->loadModel('Users');
        $userId = $this->Users->getIdFromUsername($username);
        if (empty($userId)) {
            $this->set('userExists', false);
            return;
        }

        $visibility = null;
        if ($username != CurrentUser::get('username')) {
            $visibility = 'public';
        }
        $this->paginate = $this->SentencesLists->getPaginatedLists(
            $filter, $username, $visibility
        );
        $userLists = $this->paginate();

        $this->set('userLists', $userLists);
        $this->set('filter', $filter);
        $this->set('userExists', true);
    }

    /**
     * Saves a new sentence (as if it was added from the Contribute section) and
     * add it to the list.
     * Used in AJAX request in sentences_lists.add_new_sentence_to_list.js.
     *
     * TODO refactor this; we should call the saving part of sentence controller
     *  and the adding part should be factorized with the adding part of
     *  add_sentence_to_list(), also in this controller
     *
     * @return void
     */
    public function add_new_sentence_to_list()
    {
        $result = null;
        $listId = $this->request->getData('listId');
        $sentenceText = $this->request->getData('sentenceText');

        if (!is_null($listId) && !is_null($sentenceText)) {
            $userName = $this->Auth->user('username');
            $sentenceLang = $this->LanguageDetection->detectLang(
                $sentenceText,
                $userName
            );

            $result = $this->SentencesLists->addNewSentenceToList(
                $listId,
                $sentenceText,
                $sentenceLang,
                $this->Auth->user('id')
            );

            $this->Cookie->write('most_recent_list', $listId, false, "+1 month");
        }

        $this->set('sentence', $result);
    }

    /**
     *
     * @return void
     */
    public function set_option()
    {
        $userId = CurrentUser::get('id');
        $result = $this->SentencesLists->editOption(
            $this->request->getData('listId'),
            $this->request->getData('option'),
            $this->request->getData('value'),
            $userId
        );

        $this->response->header('Content-Type: application/json');
        $this->set('result', json_encode($result['SentencesList']));
    }

    /**
     * Page to export a list.
     *
     * @param int $listId Id of the list to download
     *
     * @return void
     */
    public function download($listId = null)
    {
        if (empty($listId)) {
            return $this->redirect(array('action' => 'index'));
        }

        $list = $this->SentencesLists->getListWithPermissions(
            $listId, CurrentUser::get('id')
        );

        if (!$list['Permissions']['canView']) {
            $this->Flash->set(
                __('You do not have permission to download this list.')
            );
            return $this->redirect(array('action' => 'index'));
        }

        $count = $this->SentencesLists->getNumberOfSentences($listId);
        if ($count > SentencesList::MAX_COUNT_FOR_DOWNLOAD)
        {
            $this->flash(
                __(
                    'This list cannot be downloaded '.
                    'because it contains too many sentences.'
                ),
                array('action' => 'show', $listId)
            );
        }

        $listName = $this->SentencesLists->getNameForListWithId($listId);
        $this->set('listId', $listId);
        $this->set('listName', $listName);
    }

    /**
     * Export to csv a list
     *
     * @return void
     */

    public function export_to_csv()
    {
        $exportId = $this->request->getData('insertId');
        $translationsLang = $this->request->getData('TranslationsLang');
        $listId = $this->request->getData('id');

        if ($translationsLang === 'none') {
            $translationsLang = null;
        }

        $list = $this->SentencesLists->getListWithPermissions(
            $listId, CurrentUser::get('id')
        );

        if (!$list['Permissions']['canView']) {
            $this->Flash->set(
                __('You do not have permission to download this list.')
            );
            return $this->redirect(array('action' => 'index'));
        }

        $exportId = ($exportId === '1');
        $withTranslation = ($translationsLang !== null);

        $results = $this->SentencesLists->getSentencesAndTranslationsOnly(
            $listId, $translationsLang
        );

        $this->viewBuilder()->setLayout('ajax');

        // We specify which fields will be present in the csv.
        // Order is important.
        $fieldsList = array();
        if ($exportId === true) {
            array_push($fieldsList, 'id');
        }
        array_push($fieldsList, 'text');
        if ($withTranslation === true) {
            array_push($fieldsList, 'translation');
        }

        // send to the view
        $this->set("listId", $listId);
        $this->set("fieldsList", $fieldsList);
        $this->set("translationsLang", $translationsLang);
        $this->set("sentencesWithTranslation", $results);
    }
}
