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


    public function add_sentence($sentenceId, $correctness)
    {
        $currentUserSentence = $this->UsersSentences->find(
            'first',
            array(
                'conditions' => array(
                    'sentence_id' => $sentenceId,
                    'user_id' => CurrentUser::get('id')
                )
            )
        );

        if (empty($currentUserSentence)) {
            $data = array(
                'user_id' => CurrentUser::get('id'),
                'sentence_id' => $sentenceId,
                'correctness' => $correctness
            );
        } else {
            $data = array(
                'id' => $currentUserSentence['UsersSentences']['id'],
                'correctness' => $correctness,
                'dirty' => 0
            );
        }

        $this->UsersSentences->save($data);

        $this->set('sentenceId', $sentenceId);

        $this->render('add_delete');
    }


    public function delete_sentence($sentenceId)
    {
        $data = $this->UsersSentences->find(
            'first',
            array(
                'conditions' => array(
                    'sentence_id' => $sentenceId,
                    'user_id' => CurrentUser::get('id')
                )
            )
        );

        if ($data) {
            $id = $data['UsersSentences']['id'];
            $this->UsersSentences->delete($id, false);
        }

        $this->set('sentenceId', $sentenceId);

        $this->render('add_delete');
    }


    public function of($username, $correctnessLabel = null, $lang = null)
    {
        $this->helpers[] = 'Pagination';

        $userId = $this->User->getIdFromUsername($username);
        $correctness = $this->UsersSentences->correctnessValueFromLabel(
            $correctnessLabel
        );
        $backLink = $this->referer(array('action'=>'index'), true);
        $this->set('backLink', $backLink);
        $this->set('username', $username);
        $this->set('correctness', $correctness);
        if(empty($userId)) {
            $this->set("userExists", false);
            return;
        }
        $this->paginate = $this->UsersSentences->getPaginatedCorpusOf(
            $userId, $correctness, $lang
        );
        $corpus = $this->paginate();

        $this->set('corpus', $corpus);
        $this->set("userExists", true);
        $this->set('correctnessLabel', $correctnessLabel);
        $this->set('lang', $lang);
    }
}
?>
