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
        'LanguageDetection', 
        'Cookie',
        'CommonSentence'
    );
    // We want to make sure that people don't download long lists, which can slow down the server.
    // This is an arbitrary but easy to remember value, and most lists are shorter than this.    
    const MAX_COUNT_FOR_DOWNLOAD = 100;

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
    }


    /**
     * Displays all the lists. If user is logged in, it will also display a form to
     * add a new list and the lists that belong to that user.
     *
     * @param string $filter
     */
    public function index($filter = null)
    {
        if (isset($this->request->params['search'])) {
            $filter = $this->request->params['search'];
            $this->redirect(array('action' => 'index', $filter));
        }

        $this->paginate = $this->SentencesList->getPaginatedLists(
            $filter, null, 'public'
        );
        $allLists = $this->paginate();

        $total = $this->request->params['paging']['SentencesList']['count'];
        if (empty($filter)) {
            $title = format(
                __('All public lists ({total})', $total),
                array('total' => $total)
            );
        } else {
            $title = format(
                __('All public lists containing "{search}" ({total})', $total),
                array('total' => $total, 'search' => $filter)
            );
        }

        $this->set('allLists', $allLists);
        $this->set('filter', $filter);
        $this->set('title', $title);
    }


    public function collaborative($filter = null)
    {
        if (isset($this->request->params['search'])) {
            $filter = $this->request->params['search'];
            $this->redirect(array('action' => 'collaborative', $filter));
        }

        $this->paginate = $this->SentencesList->getPaginatedLists(
            $filter, null, 'public', 'anyone'
        );
        $allLists = $this->paginate();

        $total = $this->request->params['paging']['SentencesList']['count'];
        if (empty($filter)) {
            $title = format(
                __('Collaborative lists ({total})', $total),
                array('total' => $total)
            );
        } else {
            $title = format(
                __('Collaborative lists containing "{search}" ({total})', $total),
                array('search' => $filter, 'total' => $total)
            );
        }

        $this->set('allLists', $allLists);
        $this->set('filter', $filter);
        $this->set('title', $title);

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
            $this->redirect(array("action"=>"index"));
        }

        $list = $this->SentencesList->getList($id);

        $listId = $list['SentencesList']['id'];
        $listName = $list['SentencesList']['name'];
        $listVisibility = $list['SentencesList']['visibility'];
        $editableBy = $list['SentencesList']['editable_by'];
        $isEditableByAnyone = $editableBy == 'anyone';
        $belongsToUser = CurrentUser::get('id') == $list['SentencesList']['user_id'];

        if ($listVisibility == 'private' && !$belongsToUser) {
            $this->Session->setFlash(
                __('You do not have permission to view this list.')
            );
            $this->redirect(array("action"=>"index"));
        }

        $this->paginate = $this->SentencesSentencesLists->getPaginatedSentencesInList(
            $id, CurrentUser::getSetting('sentences_per_page'), $translationsLang
        );
        $sentencesInList = $this->paginate('SentencesSentencesLists');

        $thisListCount = $this->request->params['paging']['SentencesSentencesLists']['count'];
        $downloadability_info = $this->_get_downloadability_info($thisListCount);

        $this->set('translationsLang', $translationsLang);
        $this->set('list', $list);
        $this->set('sentencesInList', $sentencesInList);
        $this->set('canDownload', $downloadability_info['can_download']);
        $this->set('downloadMessage', $downloadability_info['message']);
        $this->set('listId', $listId);
        $this->set('listName', $listName);
        $this->set('listVisibility', $listVisibility);
        $this->set('editableBy', $editableBy);
        $this->set('belongsToUser', $belongsToUser);
        $this->set('canRemoveSentence', $belongsToUser || $isEditableByAnyone);
        $this->set('listCount', $thisListCount);
    }


    /**
     * Returns array of two elements: a bool (index = 'can_download') indicating 
     * whether list can be downloaded, and a string (index = 'message') containing 
     * an empty string (if the bool is true) or a message with details (if the 
     * bool is false).
     * 
     * @param int $count length of list
     *
     * @return string
     */
    protected function _get_downloadability_info($count)
    {
        if ($count <= self::MAX_COUNT_FOR_DOWNLOAD) 
        {
            $ret['can_download'] = true;
            $ret['message'] = "";
        }
        else
        {
            $ret['can_download'] = false;
            
            $firstSentence = __n('The download feature has been disabled for '.
                                 'this list because it contains a sentence.',
                                 'The download feature has been disabled for '.
                                 'this list because it contains {n}&nbsp;sentences.',
                                 $count, true);
            
            $secondSentence = __n('Only lists containing one sentence or fewer can be '.
                                  'downloaded. If you can edit the list, you may want '.
                                  'to split it into multiple lists.',
                                  'Only lists containing {max} or fewer sentences can be '.
                                  'downloaded. If you can edit the list, you may want '.
                                  'to split it into multiple lists.',
                                  self::MAX_COUNT_FOR_DOWNLOAD, true);
            $firstSentence = format($firstSentence, array('n' => $count));
            $secondSentence = format($secondSentence, array('max' => self::MAX_COUNT_FOR_DOWNLOAD));
            $ret['message'] = format(
            /* @translators: this string is used to concatenate two sentences.
               You typically want to change this to {firstSentence}{secondSentence}
               if your language don't use space as a word separator. */
                __('{firstSentence} {secondSentence}'),
                compact('firstSentence', 'secondSentence')
            );
        } 
        return $ret;
    }


    /**
     * Create a list.
     *
     * @return void
     */
    public function add()
    {
        $listName = $this->request->data['SentencesList']['name'];
        if (!empty($this->request->data) && trim($listName) != '') {
            $newList = array(
                'name'    => $listName,
                'user_id' => $this->Auth->user('id'),
            );
            $this->SentencesList->save($newList);
            $this->redirect(array("action"=>"show", $this->SentencesList->id));
        } else {
            $this->redirect(array("action"=>"index"));
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

        $listId = Sanitize::paranoid($listId);

        if ($this->SentencesList->isEditableByCurrentUser($listId, $userId)) {

            $this->SentencesList->id = $listId;
            if ($this->SentencesList->saveField('name', $listName)) {
                $this->set('result', $listName);
            } else {
                $this->set('result', 'error');
            }

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
        $listId = Sanitize::paranoid($listId);

        $userId = $this->Auth->user('id');

        if ($this->SentencesList->isEditableByCurrentUser($listId, $userId)) {
            $this->SentencesList->delete($listId);
            // Retrieve the 'most_recent_list' cookie, and if it matches
            // $listId, erase it. Do this even if the 'remember_list' has
            // not been set, or has been set to false.
            $mostRecentList = $this->Session->read('most_recent_list');
            if ($mostRecentList == $listId)
            {
                $mostRecentList = null;
            }
        }
        $this->redirect(array("action" => "index"));
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
        $sentenceId = Sanitize::paranoid($sentenceId);
        $listId = Sanitize::paranoid($listId);
        $userId = $this->Auth->user('id');

        $this->set('result', 'error');

        if (!$this->SentencesList->isEditableByCurrentUser($listId, $userId)) {
            return;
        }

        if ($this->SentencesList->addSentenceToList($sentenceId, $listId)) {
            $this->set('result', $listId);
            $this->Cookie->write('most_recent_list', $listId, false, "+1 month");
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
        $sentenceId = Sanitize::paranoid($sentenceId);
        $listId = Sanitize::paranoid($listId);

        $userId = $this->Auth->user('id');

        if ($this->SentencesList->isEditableByCurrentUser($listId, $userId)) {
            $isRemoved = $this->SentencesList->removeSentenceFromList(
                $sentenceId, $listId
            );
            if ($isRemoved) {
                $this->set('removed', true);
            }
        }
    }


    /**
     * Displays the lists of a specific user.
     *
     * @param string $username Username of of the user we want lists of.
     * @param string $filter   Search query on name of list.
     */
    public function of_user($username=null, $filter = null)
    {
        if (isset($this->request->params['username'])) {
            $usernameParam = $this->request->params['username'];
        }
        if (isset($this->request->params['search'])) {
            $searchParam = $this->request->params['search'];
        }

        if (!empty($usernameParam)) {
            $this->redirect(array('action' => 'of_user', $usernameParam, $searchParam));
        } else if (empty($username)) {
            $this->redirect(array('action' => 'index'));
        }

        $this->set('username', $username);
        $userId = $this->User->getIdFromUsername($username);
        if (empty($userId)) {
            $backLink = $this->referer(array('action'=>'index'), true);
            $this->set('backLink', $backLink);
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
        $sentence = null;

        if (isset($_POST['listId']) && isset($_POST['sentenceText'])) {
            $listId = Sanitize::paranoid($_POST['listId']);

            $userName = $this->Auth->user('username');
            $sentenceText = $_POST['sentenceText'];
            $sentenceLang = $this->LanguageDetection->detectLang(
                $sentenceText,
                $userName
            );

            $result = $this->SentencesList->addNewSentenceToList(
                $listId,
                $sentenceText,
                $sentenceLang
            );

            $sentence = $result['Sentence'];
            $sentence['User'] = $result['User'];
            $sentence['Transcription'] = $result['Transcription'];

            $this->Cookie->write('most_recent_list', $listId, false, "+1 month");
        }

        $this->set('sentence', $sentence);
    }


    /**
     * Set list as public. When a list is public, other people can add sentences
     * to that list.
     * Used in AJAX request in sentences_lists.set_as_public.js.
     *
     * @return void
     */
    public function set_as_public()
    {
        $listId = Sanitize::paranoid($_POST['listId']);
        $isPublic = Sanitize::paranoid($_POST['isPublic']);

        $this->SentencesList->id = $listId;
        $this->SentencesList->saveField('is_public', $isPublic);
    }


    /**
     *
     * @return void
     */
    public function set_option()
    {
        $allowedOptions = array('visibility', 'editable_by');

        $listId = Sanitize::paranoid($_POST['listId']);
        $option = $_POST['option'];
        $value = $_POST['value'];

        $userId = CurrentUser::get('id');
        $belongsToUser = $this->SentencesList->belongsTotUser($listId, $userId);

        if ($belongsToUser && in_array($option, $allowedOptions)) {
            $this->SentencesList->id = $listId;
            $this->SentencesList->saveField($option, $value);

            $result = $this->SentencesList->getList($listId);
        }

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
        $downloadability_info = $this->_get_downloadability_info($count);
        if (!$downloadability_info['can_download'])
        {
            $message = $downloadability_info['message'];
            $this->flash($message, '/sentences_lists/show/'.$listId);
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
?>
