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

App::import('Core', 'Sanitize');

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
        // user's lists
        if ($this->Auth->user('id')) {
            $myLists = $this->SentencesList->findAllByUserId(
                $this->Auth->user('id')
            );
            $this->set('myLists', $myLists);
        }

        // public lists
        $publicLists = $this->SentencesList->getPublicListsNotFromUser(
            $this->Auth->user('id')
        );
        $this->set('publicLists', $publicLists);

        // all the other lists
        $otherLists = $this->SentencesList->getNonEditableListsForUser(
            $this->Auth->user('id')
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
            $this->SentencesList->id = $id;
            
            $languages =  $this->SentencesList->Sentence->languages;
            if (isset($translationsLang) 
                AND in_array($translationsLang, $languages)
            ) {
                
                Sanitize::paranoid($translationsLang);
                $this->SentencesList->recursive = 2; // TODO need optimization
                $this->set('translationsLang', $translationsLang);
                
            } else if (isset($translationsLang) && $translationsLang == 'return') {
                return $this->SentencesList->read();
            }

            $this->set('list', $this->SentencesList->read());
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

        if (!$this->_belongs_to_current_user($id) OR !$this->Auth->user('id')) {
            if (!$this->Auth->user('id')) {
                $this->Session->setFlash(
                    sprintf(
                        __(
                            'NOTE : You can edit this list if you are 
                            <a href="%s">registered</a>.', true
                        ), 
                        "/".$this->params['lang']."/users/register"
                    )
                );
            }
            $this->redirect(array("action" => "show", $id, $translationsLang));
        } else {
            $this->SentencesList->id = $id;
            
            $languages = $this->SentencesList->Sentence->languages;
            if (isset($translationsLang) 
                AND in_array($translationsLang, $languages)
            ) {
                Sanitize::paranoid($translationsLang);
                $this->SentencesList->recursive = 2; // TODO need optimization
                $this->set('translationsLang', $translationsLang);
            }

            $this->set('list', $this->SentencesList->read());
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
        Sanitize::paranoid($_POST['id']);
        Sanitize::html($_POST['value']);
        Configure::write('debug', 0);
        if ($this->_belongs_to_current_user($_POST['id'])) {
            if (isset($_POST['value']) AND isset($_POST['id'])) {
                $this->SentencesList->id = $_POST['id'];
                if ($this->SentencesList->saveField('name', $_POST['value'])) {
                    $this->set('result', $_POST['value']);
                } else {
                    $this->set('result', 'error');
                }
            } else {
                $this->set('result', 'error');
            }
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
        if ($this->_belongs_to_current_user($listId)) {
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
        Configure::write('debug', 0);
        $this->set('s', $sentenceId);
        $this->set('l', $listId);
        if ($this->_belongs_to_current_user($listId)) {
            if ($this->SentencesList->habtmAdd('Sentence', $listId, $sentenceId)) {
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
        Configure::write('debug', 0);
        if ($this->_belongs_to_current_user($listId)) {
            if ($listName != '') {
                $newList['SentencesList']['user_id'] = $this->Auth->user('id');
                $newList['SentencesList']['name'] = $listName;
                if ($this->SentencesList->save($newList)) {
                    $this->SentencesList->habtmAdd(
                        'Sentence', $this->SentencesList->id, $sentenceId
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
        Configure::write('debug', 0);
        if ($this->_belongs_to_current_user($listId)) {
            $isRemoved = $this->SentencesList->habtmDelete(
                'Sentence', $listId, $sentenceId
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
        $lists = $this->SentencesList->findAllByUserId($userId);
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
     * Check if list belongs to current user.
     *
     * @param int $listId Id of list.
     *
     * @return bool
     */
    private function _belongs_to_current_user($listId)
    {
        $this->SentencesList->id = $listId;
        $list = $this->SentencesList->read();
        if ($list['SentencesList']['user_id'] == $this->Auth->user('id') 
            OR $list['SentencesList']['is_public'] == 1
        ) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Saves a new sentence (as if it was added from the Contribute section) and
     * add it to the list.
     * Used in AJAX request in sentences_lists.add_new_sentence_to_list.js.
     *
     * @return void
     */
    public function add_new_sentence_to_list()
    {
        if (isset($_POST['listId']) AND isset($_POST['sentenceText'])) {
            Sanitize::paranoid($_POST['listId']);
            Sanitize::paranoid($_POST['sentenceText']);

            $sentence = new Sentence();

            //detecting language
            $this->GoogleLanguageApi->text = $_POST['sentenceText'];
            $response = $this->GoogleLanguageApi->detectLang();
            $data['Sentence']['lang'] = $this->GoogleLanguageApi->google2TatoebaCode(
                $response['language']
            );
            $data['Sentence']['user_id'] = $this->Auth->user('id');
            $data['Sentence']['text'] = $_POST['sentenceText'];

            // saving
            if ($sentence->save($data)) {

                $this->SentencesList->habtmAdd(
                    'Sentence', $_POST['listId'], $sentence->id
                );
                $sentence->recursive = 0;
                $sentenceSaved = $sentence->read();
                $this->set('sentence', $sentenceSaved);
                $this->set('listId', $_POST['listId']);
                
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