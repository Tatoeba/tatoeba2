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

/**
 * Model for translations.
 *
 * @category Translations
 * @package  Models
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class Translation extends AppModel
{
    public $actsAs = array('Containable', 'Autotranscriptable');
    public $useTable = 'sentences';


    public function find($sentenceId, $languages)
    {
        $translations = $this->_getTranslationsOf($sentenceId, $languages);

        // Calling manually the trigger for afterFind of Transcriptable behavior
        // because the query to retrieve the translations is a custom query
        $results['Translation'] = $this->Behaviors->trigger(
            $this,
            'afterFind',
            array($translations['Translation'], false), 
            array('modParams' => true)
        );
        $results['IndirectTranslation'] = $this->Behaviors->trigger(
            $this,
            'afterFind',
            array($translations['IndirectTranslation'], false),
            array('modParams' => true)
        );

        return $results;
    }


    /**
     * Get translations of a given sentence and translations of translations.
     *
     * @param int   $id    Id of the sentence we want translations of.
     * @param array $langs To filter translations only in some languages.
     *
     * @return array Array of translations (direct and indirect).
     */
    private function _getTranslationsOf($id, $langs)
    {
        if (empty($langs)) {
            $langConditions = "";
        } else {
            $langs = "'".implode("','",$langs)."'";
            $langConditions = "AND p2.lang IN ($langs)";
        }

        // DA ultimate Query
        $direcTranslationsQuery = "
            SELECT
              p2.text AS translation_text,
              p2.hasaudio AS hasaudio,
              p2.id   AS translation_id,
              p2.lang AS translation_lang,
              p2.user_id AS translation_user_id,
              p2.correctness AS correctness,
              'Translation' as distance
            FROM sentences_translations AS t
              LEFT  JOIN sentences AS p2 ON t.translation_id = p2.id
            WHERE
                t.sentence_id IN ($id) $langConditions
        ";

        // query use to retrieve sentence which are already direct
        // translations
        $subQuery = "
            SELECT sentences_translations.translation_id
            FROM sentences_translations
            WHERE sentences_translations.sentence_id IN ( $id )
        ";

        $indirectTranslationQuery = "
         SELECT
              p2.text AS translation_text,
              p2.hasaudio AS hasaudio,
              p2.id   AS translation_id,
              p2.lang AS translation_lang,
              p2.user_id AS translation_user_id,
              p2.correctness AS correctness,
              'IndirectTranslation'  as distance
            FROM sentences_translations AS t
                LEFT JOIN sentences_translations AS t2
                    ON t2.sentence_id = t.translation_id
                LEFT JOIN sentences AS p2
                    ON t2.translation_id = p2.id
            WHERE
                t.sentence_id != p2.id
                AND p2.id NOT IN ( $subQuery )
                AND t.sentence_id IN ( $id )
                $langConditions
            ORDER BY 4
        ";

        $query = "
            $direcTranslationsQuery
            UNION
            $indirectTranslationQuery
        ";

        $results = $this->query($query);

        $orderedResults = array(
            "Translation" => array(),
            "IndirectTranslation" => array()
        );
        foreach ($results as $result) {
            $result = $result[0] ;
            if ($result['translation_id']) { // need to check this because
                // for sentences without translations it would otherwise
                // return an empty translation array.

                $translation = array(
                    'Translation' => array(
                        'id' => $result['translation_id'],
                        'text' => $result['translation_text'],
                        'user_id' => $result['translation_user_id'],
                        'lang' => $result['translation_lang'],
                        'hasaudio' => $result['hasaudio'],
                        'correctness' => $result['correctness']
                    )
                );

                array_push(
                    $orderedResults[$result['distance']],
                    $translation
                );
            }
        }

        return $orderedResults;
    }
}
?>