<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
        'Sentences', 'Navigation', 'Html', 'Kakasi', 'Lists'
    );
    public $components = array ('GoogleLanguageApi');
    
    /**
     * Before filter.
     * 
     * @return void
     */
    public function beforeFilter() 
    {
        parent::beforeFilter();

        // setting actions that are available to everyone, even guests
        // TODO : update this... editing lists and stuff should not be 
        // accessable to anyone
        $this->Auth->allowedActions = array('*');
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
        if (isset($id)) {
            Sanitize::paranoid($id);
            
            $list = $this->SentencesList->getSentences($id, $translationsLang);
            
            $this->set('translationsLang', $translationsLang);
            $this->set('list', $list);
        } else {
            $this->redirect(array("action"=>"index"));
        }
    }


    /**
     * Create a list.
     *
     * @return void
     */
    public function add()
    {
        Sanitize::html($this->data['SentencesList']['name']);
        if (!empty($this->data) 
            AND rtrim($this->data['SentencesList']['name']) != ''
        ) {
            $this->data['SentencesList']['user_id'] = $this->Auth->user('id');
            $this->SentencesList->save($this->data);
            $this->redirect(array("action"=>"edit", $this->SentencesList->id));
        } else {
            $this->redirect(array("action"=>"index"));
        }
    }


    /**
     * Edit list. From that page user can remove sentences from list, edit list
     * name or delete list.
     *
     * @param int    $id               Id of list.
     * @param string $translationsLang Language of translations.
     *
     * @return void
     */
    public function edit($id, $translationsLang = null)
    {
        Sanitize::paranoid($id);
        
        $userId = $this->Auth->user('id');
        
        if (!$this->SentencesList->belongsToCurrentUser($id, $userId) OR !$userId) {
            if (!$userId) {
                $this->Session->setFlash(
                    sprintf(
                        __(
                            'NOTE : You can edit this list if you are '.
                            '<a href="%s">registered</a>.', true
                        ), 
                        "/".$this->params['lang']."/users/register"
                    )
                );
            }
            $this->redirect(array("action" => "show", $id, $translationsLang));
        } else {
            $list = $this->SentencesList->getSentences($id, $translationsLang);
            $this->set('translationsLang', $translationsLang);
            $this->set('list', $list);
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
        
        Sanitize::paranoid($listId);
        Sanitize::html($listName);
        
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
        Sanitize::paranoid($listId);
        
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
        Sanitize::paranoid($sentenceId);
        Sanitize::paranoid($listId);
        
        $this->set('s', $sentenceId);
        $this->set('l', $listId);
        $userId = $this->Auth->user('id');
        if ($this->SentencesList->belongsToCurrentUser($listId, $userId)) {
            if ($this->SentencesList->addSentenceToList($sentenceId, $listId)) {
                $this->set('listId', $listId);
            } else {
                $this->set('listId', 'error');
            }
        }
    }


    /**
     * Create a new list and add a sentence to that list.
     *
     * @param int    $sentenceId Id of sentence to be added to new list.
     * @param string $listName   Name of new list.
     *
     * @return void
     */
    public function add_sentence_to_new_list($sentenceId, $listName)
    {
        Sanitize::paranoid($sentenceId);
        Sanitize::html($listName);
        
        $userId = $this->Auth->user('id');
        
        if ($this->SentencesList->belongsToCurrentUser($listId, $userId)) {
            if ($listName != '') {
                $newList['SentencesList']['user_id'] = $this->Auth->user('id');
                $newList['SentencesList']['name'] = $listName;
                if ($this->SentencesList->save($newList)) {
                    $this->SentencesList->addSentenceToList(
                        $sentenceId, $this->SentencesList->id
                    );
                    $this->set('listId', $this->SentencesList->id);
                } else {
                    $this->set('listId', 'error');
                }
            } else {
                $this->set('listId', 'error');
            }
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
        Sanitize::paranoid($sentenceId);
        Sanitize::paranoid($listId);
        
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
     *
     * @param int $userId Id of user we want lists of.
     *
     * @return void
     */
    public function of_user($userId)
    {
        $lists = $this->SentencesList->getUserLists($userId);
        $this->set('lists', $lists);
    }


    /**
     * Returns the lists that the user currently connected can add sentences to.
     * It is called in the SentencesHelper, in the displayMenu() method.
     *
     * @return array
     */
    public function choices()
    {
        return $this->SentencesList->getUserChoices($this->Auth->user('id'));
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

            $listId = $_POST['listId'];
            $sentenceText = $_POST['sentenceText'];

            Sanitize::paranoid($listId);
            Sanitize::paranoid($sentenceText);

            $sentence = new Sentence();

            //detecting language
            $sentenceLang = $this->GoogleLanguageApi->detectLang(
                $sentenceText
            );

            $data['Sentence']['user_id'] = $this->Auth->user('id');
            $data['Sentence']['text'] = $sentenceText;
            $data['Sentence']['lang'] = $sentenceLang;
            // saving
            if ($sentence->save($data)) {

                $this->SentencesList->addSentenceToList(
                    $sentence->id,
                    $listId
                );
                $sentenceSaved = $sentence->getSentenceWithId($sentence->id);
                $this->set('sentence', $sentenceSaved);
                $this->set('listId', $listId);
                
            }

        }
    }

    /**
     * Display list so that it can be printed for exercising
     * translation/romanization on paper.
     *
     * @param int    $listId       Id of list.
     * @param string $romanization 'show_romanization' or 'hide_romanization'.
     *
     * @return void
     */
    public function print_as_exercise($listId, $romanization = 'hide_romanization')
    {
        Sanitize::paranoid($listId);

        $this->layout = 'lists';
        $list = $this->SentencesList->getSentences($listId, null, $romanization);
        $this->set('list', $list);
        $this->set('romanization', $romanization);
    }


    /**
     * Display list so that it can be printed as a correction reference.
     *
     * @param int    $listId           Id of list.
     * @param string $translationsLang Language of translations.
     * @param string $romanization     'show_romanization' or 'hide_romanization'. 
     *
     * @return void
     */
    public function print_as_correction($listId, $translationsLang = 'und',
        $romanization = 'hide_romanization'
    ) {
        $this->layout = 'lists';
        Sanitize::paranoid($listId);
        
        if ( $translationsLang == 'und' ) {
        
            $translationsLang = null;
        
        } else {
        
            if ($romanization == 'hide_romanization') {
                $list = $this->SentencesList->getSentences(
                    $listId, $translationsLang
                );
            } else {
                $list = $this->SentencesList->getSentences(
                    $listId, $translationsLang, $romanization
                );
            }
            $this->set('list', $list);
            
        }
        
        $this->set('listId', $listId);
        $this->set('translationsLang', $translationsLang);
        $this->set('romanization', $romanization);
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
        $this->SentencesList->id = $_POST['list_id'];
        $this->SentencesList->saveField('is_public', $_POST['is_public']);
    }
}
?>
