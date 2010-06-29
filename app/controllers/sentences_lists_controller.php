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
        'Html',
        'Lists',
        'Menu',
        'Pagination',
        'AttentionPlease'
    );
    public $components = array ('CommonSentence');
    
    
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
        );
    }


    /**
     * Displays all the lists. If user is logged in, it will also display a form to
     * add a new list and the lists that belongs to that user.
     * 
     * @return void
     */
    public function index()
    {
        $currentUserId =  $this->Auth->user('id'); 
        // user's lists
        if ($currentUserId) {
            $myLists = $this->SentencesList->getUserLists(
                $currentUserId
            );

            $this->set('myLists', $myLists);
        }

        // public lists
        $publicLists = $this->SentencesList->getPublicListsNotFromUser(
            $currentUserId
        );
        $this->set('publicLists', $publicLists);

        // all the other lists
        $otherLists = $this->SentencesList->getNonEditableListsForUser(
            $currentUserId
        );
        $this->set('otherLists', $otherLists);
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
        
        if (!isset($id)) {
            $this->redirect(array("action"=>"index"));
        }
        
        $this->_get_sentences_for_list($id, $translationsLang, false, 10);
    }
    
    
    /**
     * Displays a list for editing purpose.
     *
     * @param int    $id               Id of list.
     * @param string $translationsLang Language of translations.
     *
     * @return mixed
     */
    public function edit($id = null, $translationsLang = null)
    {
        $id = Sanitize::paranoid($id);
        $translationsLang = Sanitize::paranoid($translationsLang);
        
        if (!isset($id)) {
            $this->redirect(array("action"=>"index"));
        }
        
        $userId = $this->Auth->user('id');
        if (!$this->SentencesList->belongsToCurrentUser($id, $userId)) {
            $this->redirect(array("action"=>"show", $id));
        }
        
        $this->_get_sentences_for_list($id, $translationsLang, true, 10);
    }
    
    /**
     * Retrieve sentences for a list. Used in show() and edit().
     * 
     * @param int    $id               Id of the list.
     * @param string $translationsLang Language of the translations.
     * @param bool   $isEditable       'true' if the sentences are editable.
     * @param int    $limit            Number of sentences per page.
     *
     * @return void
     */
    private function _get_sentences_for_list(
        $id, $translationsLang, $isEditable, $limit
    ) {
        $list = $this->SentencesList->getList($id);
        
        $this->paginate = $this->SentencesList->paramsForPaginate(
            $id, $translationsLang, $isEditable, $limit
        );
        $sentencesInList = $this->paginate('SentencesSentencesLists');
        
        $this->set('translationsLang', $translationsLang);
        $this->set('list', $list);
        $this->set('sentencesInList', $sentencesInList);
    }


    /**
     * Create a list.
     *
     * @return void
     */
    public function add()
    {
        $listName = $this->data['SentencesList']['name'];
        if (!empty($this->data) && trim($listName) != '') {
            $this->data['SentencesList']['user_id'] = $this->Auth->user('id');
            $this->SentencesList->save($this->data);
            $this->redirect(array("action"=>"edit", $this->SentencesList->id));
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
        
        if ($this->SentencesList->belongsToCurrentUser($listId, $userId)) {
            
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
        
        if ($this->SentencesList->belongsToCurrentUser($listId, $userId)) {
            $this->SentencesList->delete($listId);
        }
        $this->redirect(array("action" => "index"));
    }

    /**
     * Add sentence to a list.
     *
     * @param int $sentenceId Id of sentence to add.
     * @param int $listId     Id of list in which to add the sentence.
     *
     * @return void
     */
    public function add_sentence_to_list($sentenceId, $listId)
    {
        Configure::write('debug', 0); // Need to have debug at 0 if we want the
                                      // list to be removed from the select
                                      // right after the sentence was added.
        
        $sentenceId = Sanitize::paranoid($sentenceId);
        $listId = Sanitize::paranoid($listId);
        $userId = $this->Auth->user('id');
        
        $this->set('result', 'error');
        
        if (!$this->SentencesList->belongsToCurrentUser($listId, $userId)) {
            return;
        }
        
        if ($this->SentencesList->addSentenceToList($sentenceId, $listId)) {
            $this->set('result', $listId);
        }
    }


    /**
     * Remove sentence from a list.
     *
     * @param int $sentenceId Id of sentence to be removed from list.
     * @param int $listId     Id of list in which the sentence is.
     *
     * @return void
     */
    public function remove_sentence_from_list($sentenceId, $listId)
    {
        $sentenceId = Sanitize::paranoid($sentenceId);
        $listId = Sanitize::paranoid($listId);
        
        $userId = $this->Auth->user('id');
        
        if ($this->SentencesList->belongsToCurrentUser($listId, $userId)) {
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
     * TODO There's no view for this...
     *
     * @param int $userId Id of user we want lists of.
     *
     * @return void
     */
    public function of_user($userId)
    {
        $userId = Sanitize::paranoid($userId);
        
        $lists = $this->SentencesList->getUserLists($userId);
        $this->set('lists', $lists);
    }

    /**
     * Saves a new sentence (as if it was added from the Contribute section) and
     * add it to the list.
     * Used in AJAX request in sentences_lists.add_new_sentence_to_list.js.
     *
     * TODO refactor this, we should call the saving part of sentence controller
     *  and the adding part should be factorized with the adding part of other
     *  method of this controller
     *
     * @return void
     */
    public function add_new_sentence_to_list()
    {
        if (isset($_POST['listId']) && isset($_POST['sentenceText'])) {

            $listId = Sanitize::paranoid($_POST['listId']);
            $sentenceText = $_POST['sentenceText'];
            
            //saving
            $isSaved = $this->CommonSentence->wrapper_save_sentence(
                'auto',
                $sentenceText,
                $this->Auth->user('id')
            );

            if ($isSaved) {

                $Sentence = ClassRegistry::init('Sentence');
                $this->SentencesList->addSentenceToList(
                    $Sentence->id,
                    $listId
                );
                $sentenceSaved = $Sentence->getSentenceWithId($Sentence->id);
                $sentenceSaved['Sentence']['User'] = array( // HACK
                    'username' => $this->Auth->user('username')
                );
                $this->set('sentence', $sentenceSaved);
                $this->set('listId', $listId);
                $this->set('isSaved', true);
            } else {

                $this->set('isSaved', false);
            }
        }
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
     * Page to export a list.
     *
     * @return void
     */
    public function download($listId = null)
    {
        if (empty($listId)) {
            $this->redirect(array('action' => 'index'));
        }
        
        $listId = Sanitize::paranoid($listId);
        
        $listName = $this->SentencesList->getNameForListWithId($listId);
        $this->set('listId', $listId);
        $this->set('listName', $listName);
    }
   
    /**
     *
     *
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
        // to cakephp that he must not add the layout
        $this->layout = null;
        $this->autoLayout = false;
        // to prevent cakephp from adding debug output
        Configure::write("debug", 0); 
        
        $results = $this->SentencesList->getSentencesAndTranslationsOnly(
            $listId, $translationsLang 
        );
  
        // we specify which field will be present in the csv
        // order is important 
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
