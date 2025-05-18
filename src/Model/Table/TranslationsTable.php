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
 */
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Database\Schema\TableSchema;

class TranslationsTable extends Table
{
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('text', 'text');
        return $schema;
    }

    public function initialize(array $config)
    {
        $this->setTable('sentences');

        $this->hasMany('Transcriptions');
        $this->hasMany('Audios');
        $this->belongsToMany('IndirectTranslations', [
            'className' => 'Translations',
            'joinTable' => 'sentences_translations',
            'foreignKey' => 'sentence_id',
            'targetForeignKey' => 'translation_id'
        ]);
        $this->belongsTo('Users');

        if (Configure::read('AutoTranscriptions.enabled')) {
            $this->addBehavior('Transcriptable');
        }
    }

    /**
     * Return an SQL query that retrieves direct and indirect translations
     * fast. It returns results that mimics a regular hasMany relationship,
     * so that it interfaces well with CakePHP's Containable behavior.
     */
    public function hasManyTranslationsLikeSqlQuery($conditions = array())
    {
        $dbo = $this->getDataSource();
        $query = $this->fetchTranslationsQuery('({$__cakeID__$})', false);
        $query = array_merge(
            array(
                'table' => $dbo->fullTableName($this),
                'alias' => $this->alias,
                'conditions' => $conditions,
                'group' => null,
                'order' => null,
                'limit' => null,
            ),
            $query
        );
        $sqlQuery = $dbo->buildStatement($query, $this);
        $sqlQuery = $dbo->buildStatement(
            array(
                'fields' => array('*'),
                'table' => "($sqlQuery)",
                'alias' => 'Translation',
                'conditions' => array(),
                'group' => null,
                'order' => 'Translation.lang',
                'limit' => null,
            ),
            $this
        );
        return $sqlQuery;
    }

    /**
     * Build the array parameter for find() that allows fast retrieval
     * of direct and indirect translations of sentences of id $ids.
     *
     * @param array or int $ids Sentence(s) id the query will fetch translations of.
     * @param bool $escapeId Escape $id while building SQL internally.
     *
     * @return array find()-compatible options
     */
    public function fetchTranslationsQuery($ids, $escapeId = true)
    {
        /**
         * This query is constructed with a subquery, which finds the
         * translations ids and report the type (direct or indirect).
         * Then, we’re join()'ing with this subquery in order to get
         * all the sentences with these ids, along with the type.
         * In SQL terms, it looks like this:
         *
         *   SELECT
         *     AllTranslations.type, AllTranslations.sentence_id, Translation.*
         *   FROM
         *     sentences AS Translation,
         *     (
         *       SELECT Link.sentence_id as sentence_id,
         *              IF(Link.sentence_id = IndirectLink.translation_id,
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
        $Links = TableRegistry::getTableLocator()->get('Links');
        $subQuery = $Links->find('all')
            ->where(['Links.sentence_id IN' => $ids])
            ->join([
                'table' => 'sentences_translations',
                'alias' => 'IndirectLink',
                'type' => 'inner',
                'conditions' => ['Links.translation_id = IndirectLink.sentence_id']
            ])
            ->select([
                'sentence_id' => 'Links.sentence_id',
                'translation_id' => 'IF(Links.sentence_id = IndirectLink.translation_id, IndirectLink.sentence_id, IndirectLink.translation_id)',
                'type' => 'MIN(Links.sentence_id <> IndirectLink.translation_id)'
            ])
            ->group('translation_id');

        return $this->find('all')
            ->join([
                [
                    'table' => $subQuery,
                    'alias' => 'AllTranslations',
                    'type' => 'inner',
                    'conditions' => ['Translations.id = AllTranslations.translation_id']
                ]
            ])
            ->select([
                'type' => 'AllTranslations.type',
                'sentence_id' => 'AllTranslations.sentence_id',
                'id' => 'Translations.id',
                'text' => 'Translations.text',
                'user_id' => 'Translations.user_id',
                'lang' => 'Translations.lang',
                'script' => 'Translations.script',
                'correctness' => 'Translations.correctness',
            ])
            ->order('Translations.lang');
    }

    public function afterFind($results, $primary = false) {
        if ($primary) {
            // Direct Translation::find() call case, as
            // opposed to find('contain' => array('Translation')).
            // Let's fix getTranslationsOf()'s return value.
            foreach ($results as $i => $result) {
                foreach ($results[$i]['AllTranslations'] as $k => $v) {
                    $results[$i]['Translation'][$k] = $v;
                }
                unset($results[$i]['AllTranslations']);
            }
        }
        return $results;
    }

    /**
     * Get translations of one or more given sentences and translations of translations.
     *
     * @param int   $ids   Ids of the sentences we want translations of.
     * @param array $langs To filter translations only in some languages.
     *
     * @return array Array of translations (direct and indirect).
     */
    public function getTranslationsOf($ids, $langs)
    {
        $query = $this->fetchTranslationsQuery($ids);
        if ($langs) {
            $query->where(['Translations.lang' => $langs], ['Translations.lang' => 'string[]']);
        }
        $query->contain([
            'Transcriptions' => [
                'Users' => ['fields' => 'username']
            ]
        ]);
        return $query->toList();
    }
}
