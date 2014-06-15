<?php
/**
    Tatoeba Project, free collaborative creation of languages corpuses project
    Copyright (C) 2010  Allan SIMON (allan.simon@supinfo.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   Allan Simon <allan.simon@supinfo.com>
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Model Class used only for pagination
 *
 * @category PHP
 * @package  Tatoeba
 * @author   Allan Simon <allan.simon@supinfo.com>
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
*/
class SentenceNotTranslatedIn extends AppModel
{

    public $name = 'SentenceNotTranslatedIn';
    public $actsAs = array("Containable");
    public $useTable = false;


    /**
     * Overriding the paginate function in order to use a "raw" SQL request
     *
     * @return array
     */
    public function paginate(
        $conditions,
        $fields,
        $order,
        $limit,
        $page = 1,
        $recursive = null,
        $extra = array()
    ) {
        $recursive = -1;
        $source = $conditions['source'] ;
        $target = $conditions['notTranslatedIn'] ;
        $audioOnly = $conditions['audioOnly'];

        $source = Sanitize::paranoid($source);
        $target = Sanitize::paranoid($target);
        $audioOnly = Sanitize::paranoid($audioOnly);

        if ($page < 1) {
         $page = 1;
        }

        $limitHigh = $limit * $page;
        $limitLow = $limitHigh - $limit;

        // to add to the sql conditions, if we want only sentences with audio
        $filterAudio = '';
        if ($audioOnly == true) {
            $filterAudio = "AND Sentence.hasaudio != 'no' ";
        }

        if ($target == 'und') {
            // we want only untranslated sentences
            $sql
                = "
            SELECT distinct Sentence.id FROM sentences as Sentence
            WHERE Sentence.lang = '$source' $filterAudio
              AND Sentence.id NOT IN
              (
                SELECT s.id FROM sentences s
                  JOIN sentences_translations st ON ( s.id = st.sentence_id )
                WHERE s.lang = '$source'
              )
            ORDER BY Sentence.id DESC
            LIMIT $limitLow,$limit;
            ";
        } else {
            $sql
                = "
            SELECT distinct Sentence.id FROM sentences as Sentence
            WHERE Sentence.lang = '$source' $filterAudio
              AND Sentence.id NOT IN
              (
                SELECT DISTINCT s.id FROM sentences s
                  JOIN sentences_translations st ON ( s.id = st.sentence_id )
                  JOIN sentences t on ( st.translation_id = t.id )
                WHERE s.lang = '$source' AND t.lang = '$target'
              )
            ORDER BY Sentence.id DESC
            LIMIT $limitLow,$limit;
            ";
        }

        $result = $this->query($sql);
        return $result;
    }

    /**
     * Overriding the paginateCount function in order to use a "raw" SQL request
     *
     * @author Allan Simon
     * @author gillux (did some optimization)
     *
     * @return int Number of sentences not translated in specified language
     */

    public function paginateCount(
        $conditions = null,
        $recursive = 0,
        $extra = array()
    ) {
        $source = $conditions['source'] ;
        $target = $conditions['notTranslatedIn'] ;
        $audioOnly = $conditions['audioOnly'];

        $source = Sanitize::paranoid($source);
        $target = Sanitize::paranoid($target);
        $audioOnly = Sanitize::paranoid($audioOnly);

        // to add to the sql conditions, if we want only sentences with audio
        $filterAudio = '';
        if ($audioOnly == true) {
            $filterAudio = "AND Sentence.hasaudio != 'no' ";
        }

        // First, calculate the number of sentences.
        if ($target == 'und') { 
            // we want only untranslated sentences
            $sql
                = "
                SELECT count(distinct Sentence.id) as Count FROM sentences as Sentence
                  JOIN sentences_translations st ON ( Sentence.id = st.sentence_id )
                WHERE Sentence.lang = '$source'
                $filterAudio
                ";
        } else {
            $sql
                = "
            SELECT count(DISTINCT Sentence.id) as Count FROM sentences as Sentence
              JOIN sentences_translations st ON ( Sentence.id = st.sentence_id )
              JOIN sentences t on ( st.translation_id = t.id )
            WHERE Sentence.lang = '$source'
            AND t.lang = '$target'
            $filterAudio
            " ;
        }

        $total = 0;
        $results = $this->query($sql);
        if (isset($results[0])) {
            $translations_count = $results[0][0]['Count'];
        }

        // Then subtract this result from the total number of sentences
        // in the source language
        if ($audioOnly == true) {
            $sql = "SELECT count(Sentence.id) as Count FROM sentences as Sentence
                    WHERE Sentence.lang = '$source' $filterAudio";
            $results = $this->query($sql);
            if (isset($results[0])) {
                $total = $results[0][0]['Count'];
            }
        } else {
            // We already have that total in the languages table
            $sql = "SELECT numberOfSentences as Count FROM languages WHERE code = '$source'";
            $results = $this->query($sql);
            if (isset($results[0])) {
                $total = $results[0]['languages']['Count'];
            }

        }

        return $total - $translations_count;
    }
}
?>
