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
use App\Model\CurrentUser;
use Cake\Event\Event;

/**
 * Controller for activities (i.e. things that contributors can do in Tatoeba).
 *
 * @category Activities
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class ActivitiesController extends AppController
{
    public $components = array ('CommonSentence', 'Flash');

    /**
     * Before filter.
     *
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        // setting actions that are available to everyone, even guests
        $this->Auth->allow();
    }


    /**
     * Add new sentences.
     *
     * @return void
     */
    public function add_sentences()
    {
    }


    /**
     * Adopt sentences.
     *
     * @param string $lang
     */
    public function adopt_sentences($lang = null)
    {
        $this->helpers[] = 'CommonModules';
        $this->helpers[] = 'Pagination';

        $conditions = array('user_id' => null);
        if(!empty($lang)) {
            $conditions['lang'] = $lang;
        }

        $this->loadModel('Sentences');
        $this->paginate = array(
            'limit' => CurrentUser::getSetting('sentences_per_page'),
            'conditions' => $conditions,
            'contain' => array(
                'Transcription' => array(
                    'Users' => array('fields' => array('username')),
                ),
                'Audio' => array(
                    'Users' => array('fields' => array('username')),
                    'fields' => array('user_id'),
                ),
            ),
        );
        $results = $this->paginate('Sentences');
        $this->set('results', $results);
        $this->set('lang', $lang);
    }


    /**
     * Improve sentences.
     */
    public function improve_sentences()
    {
        $this->loadModel('Tags');
        $tagChangeName = $this->Tags->getChangeTagName();
        $tagCheckName = $this->Tags->getCheckTagName();
        $tagDeleteName = $this->Tags->getDeleteTagName();
        $tagNeedsNativeCheckName = $this->Tags->getNeedsNativeCheckTagName();
        $tagOKName = $this->Tags->getOKTagName();

        $tagChangeId = $this->Tags->getIdFromName($tagChangeName);
        $tagCheckId = $this->Tags->getIdFromName($tagCheckName);
        $tagDeleteId = $this->Tags->getIdFromName($tagDeleteName);
        $tagNeedsNativeCheckId = $this->Tags->getIdFromName($tagNeedsNativeCheckName);
        $tagOKId = $this->Tags->getIdFromName($tagOKName);

        $this->set('tagChangeName', $tagChangeName);
        $this->set('tagCheckName', $tagCheckName);
        $this->set('tagDeleteName', $tagDeleteName);
        $this->set('tagNeedsNativeCheckName', $tagNeedsNativeCheckName);
        $this->set('tagOKName', $tagOKName);

        $this->set('tagChangeId', $tagChangeId);
        $this->set('tagCheckId', $tagCheckId);
        $this->set('tagDeleteId', $tagDeleteId);
        $this->set('tagNeedsNativeCheckId', $tagNeedsNativeCheckId);
        $this->set('tagOKId', $tagOKId);
    }


    /**
     * Translate sentences.
     */
    public function translate_sentences()
    {
        $this->helpers[] = 'Languages';

        if (isset($_GET['langFrom']) && isset($_GET['langTo']))
        {
            $langFrom = $_GET['langFrom'];
            $langTo = $_GET['langTo'];
            $sort = $_GET['sort');

            $this->Cookie->write(
                'not_translated_into_lang',
                $langTo,
                false,
                '+1 month'
            );

            $this->redirect(array(
                'controller' => 'sentences',
                'action' => 'search',
                '?' => array(
                    'from' => $langFrom,
                    'to' => 'none',
                    'trans_filter' => 'exclude',
                    'trans_to' => $langTo,
                    'sort' => $sort
                )
            ));
        }
    }


    /**
     * Translate sentences of a specific user.
     *
     * @param string $username          Username.
     * @param string $lang              Language of the sentences.
     */
    public function translate_sentences_of($username, $lang = null) {
        $this->helpers[] = 'Pagination';
        $this->helpers[] = 'Languages';
        $this->helpers[] = 'CommonModules';

        $this->set('username', $username);

        $this->loadModel('Users');
        $userId = $this->Users->getIdFromUsername($username);

        if (empty($userId)) {
            $flashMessage = format(
                __("There's no user called {username}"),
                array('username' => $username)
            );
            $this->Flash->set($flashMessage);
            $this->redirect(
                array(
                    'controller' => 'users',
                    'action' => 'all'
                )
            );
        }

        $this->loadModel('Sentences');

        $conditions = array(
            'user_id' => $userId
        );
        if (!empty($lang)) {
            $conditions['Sentences.lang'] = $lang;
        }

        if (CurrentUser::isMember()) {
            $contain = $this->Sentences->contain();
        } else {
            $contain = $this->Sentences->minimalContain();
        }
        $this->paginate = array(
            'Sentences' => array(
                'fields' => $this->Sentences->fields(),
                'conditions' => $conditions,
                'contain' => $contain,
                'limit' => CurrentUser::getSetting('sentences_per_page'),
                'order' => ['Sentences.created DESC'],
            )
        );

        $results = $this->paginate('Sentences');

        $this->set('results', $results);
        $this->set('lang', $lang);
    }
}
