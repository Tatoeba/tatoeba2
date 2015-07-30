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
class CorpusController extends AppController
{
    public $uses = array('UsersSentences', 'User');
    public $helper = array();


    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->Auth->allowedActions = array(
            'of'
        );
    }


    public function of($username, $correctnessLabel = null, $lang = null)
    {
        $this->helpers[] = 'Pagination';

        $userId = $this->User->getIdFromUsername($username);
        $correctness = $this->UsersSentences->correctnessValueFromLabel(
            $correctnessLabel
        );
        $this->paginate = $this->UsersSentences->getPaginatedCorpusOf(
            $userId, $correctness, $lang
        );
        $corpus = $this->paginate();

        $this->set('corpus', $corpus);
        $this->set('username', $username);
        $this->set('correctness', $correctness);
        $this->set('correctnessLabel', $correctnessLabel);
        $this->set('lang', $lang);
    }
}
?>
