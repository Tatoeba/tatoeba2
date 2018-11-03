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
        'Cookie',
        'CommonSentence'
    );

    public $uses = array('SentencesList', 'SentencesSentencesLists', 'User');

    /**
     * Before filter.
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->Auth->allowedActions = array(
            'index',
            'show',
            'export_to_csv',
            'of_user',
            'download',
            'search',
            'collaborative'
        );

        $this->Security->unlockedActions = array(
            'set_option',
            'save_name',
            'add_new_sentence_to_list'
        );
    }


    /**
     * Displays all the lists. If user is logged in, it will also display a form to
     * add a new list and the lists that belong to that user.
     *
     * @param string $filter
     */
    public function index($filter = null)
    {
        if (isset($this->request->query['search'])) {
            $filter = $this->request->query['search'];
            $this->redirect(array('action' => 'index', $filter));
        }

        $this->paginate = $this->SentencesList->getPaginatedLists(
            $filter, null, 'public'
        );
        $allLists = $this->paginate();

        $this->set('allLists', $allLists);
        $this->set('filter', $filter);
    }


    public function collaborative($filter = null)
    {
        if (isset($this->request->query['search'])) {
            $filter = $this->request->query['search'];
            $this->redirect(array('action' => 'collaborative', $filter));
        }

        $this->paginate = $this->SentencesList->getPaginatedLists(
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
        $id = Sanitize::paranoid($id);
        $translationsLang = Sanitize::paranoid($translationsLang);
        if (empty($translationsLang)) {
            $translationsLang = 'none';
        }

        if (!isset($id)) {
            $this->redirect(array('action' => 'index'));
        }

        $list = $this->SentencesList->getListWithPermissions(
            $id, CurrentUser::get('id')
        );

        if (!$list['Permissions']['canView']) {
            $this->Flash->set(
                __('You do not have permission to view this list.')
            );
            $this->redirect(array('action' => 'index'));
        }

        $this->paginate = $this->SentencesSentencesLists->getPaginatedSentencesInList(
            $id, CurrentUser::getSetting('sentences_per_page'), $translationsLang
        );
        $sentencesInList = $this->paginate('SentencesSentencesLists');

        $this->set('translationsLang', $translationsLang);
        $this->set('list', $list['SentencesList']);
        $this->set('user', $list['User']);
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
        $list = $this->SentencesList->createList(
            $this->request->data['SentencesList']['name'], 
            $this->Auth->user('id')
        );
        
        if (isset($list['SentencesList']['id'])) {
            $this->redirect(array('action' => 'show', $list['SentencesList']['id']));
        } else {
            $this->redirect(array('action' => 'index'));
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
        $listId = substr($_POST['id'], 1);
        $listName = $_POST['value'];
        
        if ($this->SentencesList->editName($listId, $listName, $userId)) {
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
        if ($this->SentencesList->deleteList($listId, $userId)) {
            // Retrieve the 'most_recent_list' cookie, and if it matches
            // $listId, erase it. Do this even if the 'remember_list' has
            // not been set, or has been set to false.
            $mostRecentList = $this->Session->read('most_recent_list');
            if ($mostRecentList == $listId)
            {
                $mostRecentList = null;
            }
        }
        
        $this->redirect(array('action' => 'index'));
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
        if ($this->SentencesList->addSentenceToList($sentenceId, $listId, $userId)) {
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
        $isRemoved = $this->SentencesList->removeSentenceFromList(
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
        if (isset($this->request->query['username'])) {
            $usernameParam = $this->request->query['username'];
        }
        if (isset($this->request->query['search'])) {
            $searchParam = $this->request->query['search'];
        }

        if (!empty($usernameParam)) {
            $this->redirect(array('action' => 'of_user', $usernameParam, $searchParam));
        } else if (empty($username)) {
            $this->redirect(array('action' => 'index'));
        }

        $this->set('username', $username);
        $userId = $this->User->getIdFromUsername($username);
        if (empty($userId)) {
            $this->set("userExists", false);
            return;
        }

        $visibility = null;
        if ($username != CurrentUser::get('username')) {
            $visibility = 'public';
        }
        $this->paginate = $this->SentencesList->getPaginatedLists(
            $filter, $username, $visibility
        );
        $userLists = $this->paginate('SentencesList');

        $this->set('userLists', $userLists);
        $this->set('filter', $filter);
        $this->set("userExists", true);
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

        if (isset($_POST['listId']) && isset($_POST['sentenceText'])) {
            $listId = $_POST['listId'];
            $userName = $this->Auth->user('username');
            $sentenceText = $_POST['sentenceText'];
            $sentenceLang = $this->LanguageDetection->detectLang(
                $sentenceText,
                $userName
            );

            $result = $this->SentencesList->addNewSentenceToList(
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
        $result = $this->SentencesList->editOption(
            $_POST['listId'], $_POST['option'], $_POST['value'], $userId
        );

        $this->header('Content-Type: application/json');
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
            $this->redirect(array('action' => 'index'));
        }

        $count = $this->SentencesList->getNumberOfSentences($listId);
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

        $listId = Sanitize::paranoid($listId);

        $listName = $this->SentencesList->getNameForListWithId($listId);
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
        $exportId = $_POST['data']['SentencesList']['insertId'];
        $translationsLang = $_POST['data']['SentencesList']['TranslationsLang'];
        $listId = $_POST['data']['SentencesList']['id'];

        // Sanitize part
        $exportId = Sanitize::paranoid($exportId);
        $translationsLang = Sanitize::paranoid($translationsLang);
        $listId = Sanitize::paranoid($listId);

        if ($translationsLang === "none") {
            $translationsLang = null;
        }

        $exportId = ($exportId === "1");
        $withTranslation = ($translationsLang !== null);

        // as the view is a file to be downloaded we need to say
        // to cakephp that it must not add the layout
        $this->layout = null;
        $this->autoLayout = false;
        // to prevent cakephp from adding debug output
        Configure::write("debug", 0);

        $results = $this->SentencesList->getSentencesAndTranslationsOnly(
            $listId, $translationsLang
        );

        // We specify which fields will be present in the csv.
        // Order is important.
        $fieldsList = array();
        if ($exportId === true) {
            array_push($fieldsList, "Sentence.id");
        }
        array_push($fieldsList, 'Sentence.text');
        if ($withTranslation === true) {
            array_push($fieldsList, "Translation.text");
        }

        // send to the view
        $this->set("listId", $listId);
        $this->set("fieldsList", $fieldsList);
        $this->set("translationsLang", $translationsLang);
        $this->set("sentencesWithTranslation", $results);
    }
}
