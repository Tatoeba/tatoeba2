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

/**
 * Model for Languages.
 *
 * @category Language
 * @package  Models
 * @author   SIMON   Allan   <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class Language extends AppModel
{
    public $name = 'Language';

    /**
     * Return the id associated with the given lang string
     *
     * @param $langCode ISO code of the language for which we want to get the id.
     *
     * @return int Id of the language
     */
    public function getIdFromLang($langCode)
    {
        if ($langCode == null || $langCode == '')
        {
            return null;
        }
        else
        {
            $result = $this->find(
                'first',
                array(
                    'fields' => array('id'),
                    'contain' => array(),
                    'conditions' => array ('code' => $langCode),
                )
            );
            return $result['Language']['id'];
        }
    }


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
                    'group_1',
                    'group_2',
                    'group_3',
                    'group_4'
                ),
                'order' => array('sentences DESC'),
                'limit' => $limit
            )
        );

        return $results ;
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
            UPDATE languages SET numberOfSentences = numberOfSentences + 1
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
            UPDATE languages SET numberOfSentences = numberOfSentences - 1
                WHERE $endOfQuery ;
        ";
        $this->query($query);
    }
}
