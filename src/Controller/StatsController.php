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
    /**
     *
     */
    function sentences_by_language() {
        $this->loadModel('Languages');
        $this->loadModel('Sentences');
        $stats = $this->Languages->getSentencesStatistics();
        $audioStats = $this->Languages->getAudioStats();
        $totalSentences = $this->Sentences->find()->count();

        $this->set('stats', $stats);
        $this->set('audioStats', $audioStats);
        $this->set('totalSentences', $totalSentences);
    }


    /**
     *
     */
    function users_languages()
    {
        $this->loadModel('Languages');
        $stats = $this->Languages->getUsersLanguagesStatistics();
        $this->set('stats', $stats);
    }


    /**
     *
     */
    function native_speakers()
    {
        $this->loadModel('Languages');
        $stats = $this->Languages->getNativeSpeakersStatistics();
        $this->set('stats', $stats);
    }
}
