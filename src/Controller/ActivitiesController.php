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
 * @link     https://tatoeba.org
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
 * @link     https://tatoeba.org
 */
class ActivitiesController extends AppController
{
    public $components = array ('CommonSentence', 'Flash');

    /**
     * Adopt sentences.
     *
     * @param string $lang
     */
    public function adopt_sentences($lang = null)
    {
        $this->helpers[] = 'CommonModules';
        $this->helpers[] = 'Pagination';
        
        $this->loadModel('Sentences');
        $query = $this->Sentences
            ->find('filteredTranslations', ['translationLang' => 'none'])
            ->find('hideFields')
            ->select($this->Sentences->fields())
            ->contain($this->Sentences->contain())
            ->where(['user_id IS' => null]);

        if(!empty($lang)) {
            $query->where(['lang' => $lang]);
        }

        $this->paginate = [
            'limit' => CurrentUser::getSetting('sentences_per_page'),
        ];
        $results = $this->paginate($query);
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
        
        $langFrom = $this->request->getQuery('langFrom');
        if ($langFrom)
        {
            $sort = $this->request->getQuery('sort', 'created');
            $langTo = $this->request->getQuery('langTo');

            $this->Cookie->write(
                'not_translated_into_lang',
                $langTo,
                false,
                '+1 month'
            );

            $searchParams = array(
                'from' => $langFrom,
                'sort' => $sort
            );
            if ($this->request->getQuery('excludeLangTo') == 'yes') {
                $searchParams['trans_filter'] = 'exclude';
                $searchParams['trans_to'] = $langTo;
            }
            $this->redirect(array(
                'controller' => 'sentences',
                'action' => 'search',
                '?' => $searchParams
            ));
        }
        $notTranslatedInto = $this->Cookie->read('not_translated_into_lang');
        $this->set('not_translated_into_lang', $notTranslatedInto);
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
        $query = $this->Sentences
            ->find('filteredTranslations')
            ->find('hideFields')
            ->select($this->Sentences->fields())
            ->where(['user_id' => $userId])
            ->contain($this->Sentences->contain(['translations' => true]))
            ->order(['Sentences.created' => 'DESC']);

        if (!empty($lang)) {
            $query->where(['Sentences.lang' => $lang]);
        }

        $this->paginate = [
            'limit' => CurrentUser::getSetting('sentences_per_page'),
        ];

        try {
            $results = $this->paginate($query);
        } catch (\Cake\Http\Exception\NotFoundException $e) {
            return $this->redirectPaginationToLastPage();
        }

        $this->set('results', $results);
        $this->set('lang', $lang);
    }
}
