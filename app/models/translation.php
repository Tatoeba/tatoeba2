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
    public $actsAs = array('Containable');
    public $useTable = 'sentences';


    public function find($sentenceId, $languages)
    {
        $translations = $this->_getTranslationsOf($sentenceId, $languages);

        $orderedTranslations = array(
            'Translation' => array(),
            'IndirectTranslation' => array()
        );
        $map = array(
            '0' => 'Translation',
            '1' => 'IndirectTranslation',
        );
        foreach ($translations as $record) {
            $distance = $record['AllTranslations']['type'];
            $orderedTranslations[ $map[$distance] ][] = array(
                'Translation' => $record['Translation']
            );
        };

        return $orderedTranslations;
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
        /**
         * This query is constructed with a subquery, which finds the
         * translations ids and report the type (direct or indirect).
         * Then, we’re join()'ing with this subquery in order to get
         * all the sentences with these ids, along with the type.
         * In SQL terms, it looks like this:
         *
         *   SELECT AllTranslations.type, Translation.*
         *   FROM
         *     sentences AS Translation,
         *     (
         *       SELECT IF(Link.sentence_id = IndirectLink.translation_id,
         *                IndirectLink.sentence_id, IndirectLink.translation_id)
         *                AS translation_id,
         *              MIN(Link.sentence_id <> IndirectLink.translation_id)
         *                AS 'type'
         *       FROM sentences_translations AS Link
         *       INNER JOIN sentences_translations AS IndirectLink
         *             ON (Link.translation_id = IndirectLink.sentence_id)
         *       WHERE Link.sentence_id = $id
         *       GROUP BY translation_id
         *     ) AS AllTranslations
         *   WHERE Translation.id = AllTranslations.translation_id
         *   ORDER BY Translation.lang;
         */
        $dbo = $this->getDataSource();
        $subQuery = $dbo->buildStatement(
            array(
                'fields' => array(
                    "IF(Link.sentence_id = IndirectLink.translation_id, "
                        ."IndirectLink.sentence_id, "
                        ."IndirectLink.translation_id) "
                        ."AS translation_id",
                    "MIN(Link.sentence_id <> IndirectLink.translation_id) "
                        ."AS 'type'",
                ),
                'table' => 'sentences_translations',
                'alias' => 'Link',
                'limit' => null,
                'offset' => null,
                'joins' => array(array(
                    'table' => 'sentences_translations',
                    'alias' => 'IndirectLink',
                    'type' => 'inner',
                    'conditions' => array(
                        'Link.translation_id = IndirectLink.sentence_id'
                    ),
                )),
                'conditions' => array('Link.sentence_id' => $id),
                'order' => null,
                'group' => 'translation_id',
            ),
            $this
        );

        $conditions = $langs ? array('Translation.lang' => $langs) : array();
        return parent::find('all', array(
            'joins' => array(
                array(
                    'table' => "($subQuery)",
                    'alias' => 'AllTranslations',
                    'conditions' => array('Translation.id = AllTranslations.translation_id'),
                )
            ),
            'conditions' => $conditions,
            'fields' => array(
                'AllTranslations.type',
                'Translation.id',
                'Translation.text',
                'Translation.user_id',
                'Translation.lang',
                'Translation.hasaudio',
                'Translation.correctness',
            ),
            'order' => array('Translation.lang'),
            'contain' => array(),
        ));
    }
}
?>
