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

App::import('Core', 'Sanitize');

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
class SentenceNotTranslatedInto extends AppModel
{

    public $name = 'SentenceNotTranslatedInto';
    public $actsAs = array("Containable");
    public $useTable = false;


    /**
     * Overriding the paginate function in order to speed up
     * things using custom queries.
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
        $source = $conditions['source'] ;
        $target = $conditions['notTranslatedInto'] ;
        $audioOnly = $conditions['audioOnly'];

        $dbo = $this->getDataSource();

        if ($target == 'und') {
            // we want only untranslated sentences
            $subQuery = $dbo->buildStatement(
                array(
                    'fields' => array('Sentence.id'),
                    'table' => 'sentences',
                    'alias' => 'Sentence',
                    'limit' => null,
                    'joins' => array(array(
                        'table' => 'sentences_translations',
                        'alias' => 'Link',
                        'conditions' => array(
                            'Sentence.id = Link.sentence_id'
                        ),
                    )),
                    'conditions' => array('Sentence.lang' => $source),
                    'order' => null,
                    'group' => null,
                ),
                $this
            );
        } else {
            $subQuery = $dbo->buildStatement(
                array(
                    'fields' => array('Link.sentence_id'),
                    'table' => 'sentences_translations',
                    'alias' => 'Link',
                    'limit' => null,
                    'conditions' => array(
                        'Link.sentence_lang' => $source,
                        'Link.translation_lang' => $target,
                    ),
                    'order' => null,
                    'group' => null,
                ),
                $this
            );
        }
        $notTranslatedInCondition
            = $dbo->expression("Sentence.id NOT IN ($subQuery)");

        $conditions = array(
            'Sentence.lang' => $source,
            $notTranslatedInCondition,
        );
        $joins = array();
        if ($audioOnly == true) {
            $joins[] = array(
                'type' => 'inner',
                'table' => 'audios',
                'alias' => 'Audio',
                'conditions' => array('Sentence.id = Audio.sentence_id'),
            );
        }

        $Sentence = ClassRegistry::init('Sentence');
        $options = compact('fields', 'conditions', 'joins', 'order', 'limit', 'page');
        $result = $Sentence->find('all', array_merge($options, $extra));

        return $result;
    }

    /**
     * Overriding the paginateCount function in order to use a "raw" SQL request
     *
     * @author Allan Simon
     * @author gillux (did some optimization)
     *
     * @return int Number of sentences not translated into specified language
     */

    public function paginateCount(
        $conditions = null,
        $recursive = 0,
        $extra = array()
    ) {
        $source = $conditions['source'] ;
        $target = $conditions['notTranslatedInto'] ;
        $audioOnly = $conditions['audioOnly'];

        $source = Sanitize::paranoid($source);
        $target = Sanitize::paranoid($target);
        $audioOnly = Sanitize::paranoid($audioOnly);

        if ($audioOnly == true) {
            $sql = $this->_paginateCountSqlWithAudio($source, $target);
        } else {
            $sql = $this->_paginateCountSqlWithoutAudio($source, $target);
        }

        $total = 0;
        $results = $this->query($sql);
        if (isset($results[0])) {
            $translations_count = $results[0][0]['Count'];
        }

        // Then subtract this result from the total number of sentences
        // in the source language
        if ($audioOnly == true) {
            $sql = "SELECT count(Sentence.id) as Count
                    FROM sentences as Sentence
                    JOIN audios ON (Sentence.id = audios.sentence_id)
                    WHERE Sentence.lang = '$source'";
            $results = $this->query($sql);
            if (isset($results[0])) {
                $total = $results[0][0]['Count'];
            }
        } else {
            // We already have that total in the languages table
            $sql = "SELECT sentences as Count FROM languages WHERE code = '$source'";
            $results = $this->query($sql);
            if (isset($results[0])) {
                $total = $results[0]['languages']['Count'];
            }
        }

        return $total - $translations_count;
    }


    private function _paginateCountSqlWithAudio($source, $target)
    {
        if ($target == 'und') {

            $sql = "SELECT count(distinct Sentence.id) as Count
            FROM sentences as Sentence
            JOIN sentences_translations st ON ( Sentence.id = st.sentence_id )
            JOIN audios ON (Sentence.id = audios.sentence_id)
            WHERE Sentence.lang = '$source'";

        } else {

            $sql = " SELECT count(DISTINCT Sentence.id) as Count
            FROM sentences as Sentence
            JOIN sentences_translations st ON ( Sentence.id = st.sentence_id )
            JOIN sentences t on ( st.translation_id = t.id )
            JOIN audios ON (Sentence.id = audios.sentence_id)
            WHERE Sentence.lang = '$source'
            AND t.lang = '$target'";
        }

        return $sql;
    }


    private function _paginateCountSqlWithoutAudio($source, $target)
    {
        if ($target == 'und') {

            $sql = "SELECT count(DISTINCT sentence_id) as Count
            FROM sentences_translations
            WHERE sentence_lang = '$source'";

        } else {

            $sql = "SELECT count(DISTINCT sentence_id) as Count
            FROM sentences_translations
            WHERE sentence_lang = '$source'
            AND translation_lang = '$target'";

        }

        return $sql;
    }
}
