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


/**
 * Controller for users sentences.
 *
 * @category SentencesLists
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class CollectionsController extends AppController
{
    public $uses = array('UsersSentences', 'User');
    public $helpers = array('CommonModules');

    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->Auth->allowedActions = array(
            'of'
        );
    }

    /**
     * Add a sentence to the user's sentence collection.
     *
     * @param int $sentenceId  Sentence ID.
     * @param int $correctness Correctness value.
     */
    public function add_sentence($sentenceId, $correctness)
    {
        $this->UsersSentences->saveSentence(
            $sentenceId, $correctness, CurrentUser::get('id')
        );

        $this->set('sentenceId', $sentenceId);

        $this->render('add_delete');
    }

    /**
     * Delete a sentence from the user's sentence collection.
     *
     * @param int $sentenceId Sentence ID
     */
    public function delete_sentence($sentenceId)
    {
        $this->UsersSentences->deleteSentence(
            $sentenceId, CurrentUser::get('id')
        );

        $this->set('sentenceId', $sentenceId);

        $this->render('add_delete');
    }

    /**
     * Get sentences of user for given correctness level.
     *
     * @param  string $username         Username of user to get sentences for.
     * @param  string $correctnessLabel Label for correctness value.
     * @param  string $lang             Language.
     *
     * @return void
     */
    public function of($username, $correctnessLabel = null, $lang = null)
    {
        $this->helpers[] = 'Pagination';

        $userId = $this->User->getIdFromUsername($username);

        $this->set('username', $username);

        if(empty($userId)) {
            $this->set('userExists', false);
            return;
        }

        $this->paginate = $this->UsersSentences->getPaginatedCorpusOf(
            $userId, $correctnessLabel, $lang
        );
        $corpus = $this->paginate();

        $this->set('corpus', $corpus);
        $this->set('userExists', true);
        $this->set('correctnessLabel', $correctnessLabel);
        $this->set('lang', $lang);
    }
}
