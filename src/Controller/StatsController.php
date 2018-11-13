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
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

/**
 * Controller for stats.
 *
 * @category SentenceComments
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class StatsController extends AppController
{
    public $uses = array('Language', 'Sentence', 'Audio');

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
     *
     */
    function sentences_by_language() {
        $stats = $this->Language->getSentencesStatistics();
        $audioStats = $this->Language->getAudioStats();
        $totalSentences = $this->Sentence->getTotalNumberOfSentences();

        $this->set('stats', $stats);
        $this->set('audioStats', $audioStats);
        $this->set('totalSentences', $totalSentences);
    }


    /**
     *
     */
    function users_languages()
    {
        $stats = $this->Language->getUsersLanguagesStatistics();
        $this->set('stats', $stats);
    }


    /**
     *
     */
    function native_speakers()
    {
        $stats = $this->Language->getNativeSpeakersStatistics();
        $this->set('stats', $stats);
    }
}
