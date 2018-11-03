<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 SIMON   Allan   <allan.simon@supinfo.com>
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
 * @author   SIMON   Allan   <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
namespace App\Model;


/**
 * Model for Languages.
 *
 * @category Language
 * @package  Models
 * @author   SIMON   Allan   <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

App::uses('Sanitize', 'Utility');

class Language extends AppModel
{
    public $name = 'Language';

    const MAX_LEVEL = 5;

    /**
     * Return stats for number of sentences per language.
     *
     * @param int $limit Specifying a limit will only take the top languages
     *                   with the most sentences.
     *
     * @return array
     */
    public function getSentencesStatistics($limit = null)
    {
        $results = $this->find(
            'all',
            array(
                'fields' => array(
                    'code',
                    'sentences',
                    'audio',
                ),
                'order' => array('sentences DESC'),
                'limit' => $limit
            )
        );

        return $results ;
    }

    /**
     * Return stats for number of audio per language.
     *
     * @return array
     */
    public function getAudioStats()
    {
        $results = $this->find(
            'all',
            array(
                'conditions' => array('audio >' => 0),
                'fields' => array(
                    'code',
                    'audio',
                ),
                'order' => array('audio DESC')
            )
        );

        $stats = array();
        foreach ($results as $result) {
            $stats[] = array(
                'lang' => $result['Language']['code'],
                'total' => $result['Language']['audio']
            );
        }

        return $stats;
    }


    /**
     * Return stats for number of members who speak the language in each group of
     * users.
     *
     * @return array
     */
    public function getUsersLanguagesStatistics()
    {
        $results = $this->find(
            'all',
            array(
                'conditions' => array('code !=' => null),
                'fields' => array(
                    'code',
                    'level_5',
                    'level_4',
                    'level_3',
                    'level_2',
                    'level_1',
                    'level_0',
                    'level_unknown',
                    '(level_5 + level_4 + level_3 + level_2 + level_1 + level_0 + level_unknown) as total'
                ),
                'order' => array('total DESC'),
            )
        );

        return $results ;
    }


    /**
     *
     */
    public function getNativeSpeakersStatistics()
    {
        $results = $this->find(
            'all',
            array(
                'conditions' => array('code !=' => null),
                'fields' => array(
                    'code',
                    'group_1',
                    'group_2',
                    'group_3',
                    'group_4',
                    '(group_1 + group_2 + group_3 + group_4) as total'
                ),
                'order' => array('total DESC')
            )
        );

        return $results;
    }


    /**
     * Increment stats for specified language.
     *
     * @param $langCode Code of the language which should be incremented.
     *
     * @return void
     */
    public function incrementCountForLanguage($langCode)
    {
        $langCode = Sanitize::paranoid($langCode);
        $endOfQuery = "code = '$langCode'";

        if ($langCode == '' or $langCode == null) {
            $endOfQuery = 'code is null';
        }

        $query = "
            UPDATE languages SET sentences = sentences + 1
                WHERE $endOfQuery ;
        ";
        $this->query($query);
    }


    /**
     * Decrement stats for specified language.
     *
     * @param $langCode Code of the language which should be incremented.
     *
     * @return void
     */
    public function decrementCountForLanguage($langCode)
    {
        $langCode = Sanitize::paranoid($langCode);
        $endOfQuery = "code = '$langCode'";

        if ($langCode == '' or $langCode == null) {
            $endOfQuery = 'code is null';
        }

        $query = "
            UPDATE languages SET sentences = sentences - 1
                WHERE $endOfQuery ;
        ";
        $this->query($query);
    }


    public function incrementCountForGroup($langCode, $groupId)
    {
        $groupColumn = $this->_groupColumnName($groupId);

        $this->updateAll(
            array($groupColumn => "$groupColumn + 1"),
            array('code' => $langCode)
        );
    }


    public function decrementCountForGroup($langCode, $groupId)
    {
        $groupColumn = $this->_groupColumnName($groupId);

        $this->updateAll(
            array($groupColumn => "$groupColumn - 1"),
            array('code' => $langCode)
        );
    }


    private function _groupColumnName($groupId)
    {
        return 'Language.group_'.$groupId;
    }


    public function incrementCountForLevel($langCode, $level)
    {
        $levelColumn = $this->_levelColumnName($level);

        $this->updateAll(
            array($levelColumn => "$levelColumn + 1"),
            array('code' => $langCode)
        );
    }


    public function decrementCountForLevel($langCode, $level)
    {
        $levelColumn = $this->_levelColumnName($level);

        $this->updateAll(
            array($levelColumn => "$levelColumn - 1"),
            array('code' => $langCode)
        );
    }


    private function _levelColumnName($level)
    {
        if (isset($level)) {
            return 'Language.level_'.$level;
        } else {
            return 'Language.level_unknown';
        }

    }
}
