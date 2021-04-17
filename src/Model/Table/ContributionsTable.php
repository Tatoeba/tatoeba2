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
 * @link     https://tatoeba.org
 */
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Database\Schema\TableSchema;
use App\Model\CurrentUser;
use Cake\Core\Configure;
use Cake\I18n\Time;


/**
 * Model for contributions.
 *
 * @category Contributions
 * @package  Models
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class ContributionsTable extends Table
{
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('text', 'text');
        return $schema;
    }

    public function initialize(array $config)
    {
        $this->belongsTo('Users');
        $this->belongsTo('Sentences');
        $this->belongsTo('Translations');

        $this->addBehavior('LimitResults');
    }

    public function logSentence($event) {
        $data = $event->getData('data');
        $created = $event->getData('created');
        $sentenceId = $event->getData('id');

        if ($data->isDirty('text')) {
            $sentenceLang = $data->lang ? $data->lang : null;
            $sentenceScript = $data->script ? $data->script : null;
            $sentenceAction = 'update';
            $sentenceText = $data->text;
            if ($created) {
                $sentenceAction = 'insert';
                $this->Sentences->Languages->incrementCountForLanguage($sentenceLang);
            }
            $this->saveSentenceContribution(
                $sentenceId,
                $sentenceLang,
                $sentenceScript,
                $sentenceText,
                $sentenceAction
            );
        }

        if ($data->isDirty('license')) {
            $newLog = $this->newEntity(array(
                'sentence_id' => $sentenceId,
                'user_id' => CurrentUser::get('id'),
                'datetime' => date("Y-m-d H:i:s"),
                'action' => $created ? 'insert' : 'update',
                'type' => 'license',
                'text' => $data['license'],
            ));
            $this->save($newLog);
        };
    }

    public function paginateCount($conditions = null, $recursive = 0, $extra = array())
    {
        $botsCondition = array('user_id' => Configure::read('Bots.userIds'));
        if (is_null($conditions)
            || (isset($conditions['NOT']) && $conditions['NOT'] == $botsCondition))
        {
            return $this->estimateRowCount($this->table);
        }
        else
        {
            $parameters = compact('conditions');
            if ($recursive != $this->recursive) {
                $parameters['recursive'] = $recursive;
            }
            return $this->find('count', array_merge($parameters, $extra));
        }
    }

    private function estimateRowCount($tableName)
    {
        $db = $this->getDataSource();
        $alias = 'TABLES';
        $rowName = 'TABLE_ROWS';
        $query = array(
            'table' => 'INFORMATION_SCHEMA.TABLES',
            'alias' => $alias,
            'conditions' => array(
                'TABLE_NAME' => $tableName,
                'TABLE_SCHEMA' => $db->config['database'],
            ),
            'fields' => array($rowName),
        );
        $sql = $db->buildStatement($query, $this);
        $result = $this->query($sql);
        return $result[0][$alias][$rowName];
    }

    /**
     * Get number of contributions made by a given user
     *
     * @param int $userId Id of user.
     *
     * @return array
     */
    public function numberOfContributionsBy($userId)
    {
        return $this->find()
            ->where(['user_id' => $userId])
            ->count();
    }


    /**
     * Return contributions related to specified sentence.
     *
     * @param int $sentenceId Id of the sentence.
     *
     * @return array
     */
    public function getContributionsRelatedToSentence($sentenceId)
    {
        $query = $this->find()
            ->select([
                'Contributions.sentence_lang',
                'Contributions.script',
                'Contributions.text',
                'Contributions.translation_id',
                'Contributions.action',
                'Contributions.id',
                'Contributions.datetime',
                'Contributions.type',
                'Users.username',
                'Users.id',
                'Translations.text',
            ])
            ->where(['Contributions.sentence_id' => $sentenceId])
            ->contain([
                'Users' => function ($q) {
                    return $q->select(['username', 'id']);
                },
                'Translations',
            ])
            ->order('datetime');

        return $query->all();
    }

    /**
     * Get last contributions in a specific language if language is specified.
     * 'und' will retrieve in all languages.
     *
     * @param int    $limit Number of contributions.
     * @param string $lang  Language of contributions.
     *
     * @return array
     */
    public function getLastContributions($limit, $lang = 'und')
    {
        if (!is_numeric($limit)) {
            return [];
        }
        $query = $this->find()
            ->select([
                'sentence_id',
                'sentence_lang',
                'script',
                'text',
                'datetime',
                'action'
            ])
            ->where(['type' => 'sentence'])
            ->orderDesc('datetime')
            ->limit($limit)
            ->contain(['Users' => function ($q) {
                return $q->select(['id', 'username', 'image']);
            }]);

        $query = $this->excludeBots($query);

        if ($lang == 'und' || empty($lang)) {
            $this->setTable('last_contributions');
        } else {
            $query = $query->where(['sentence_lang' => $lang]);
        }

        return $query->all();
    }

    /**
    * Return number of contributions for current day since midnight.
    *
    * @return int
    */

    public function getTodayContributions()
    {
        return $this->find()
            ->where([
                'datetime >' => Time::now()->format('Y-m-d'),
                'translation_id IS NULL',
                'action' => 'insert',
                'type !=' => 'license'
            ])
            ->count();
    }


    /**
     * update the language of all the entries for a specific sentence
     * it is used as it increase a lot perfomance for contributions logs
     * even if the join is more "pretty"
     *
     * @param int $sentence_id the sentence to be updated
     * @param int $lang        the new lang
     *
     * @return void
     */
    public function updateLanguage($sentence_id, $lang)
    {
        $this->updateAll(
            ['sentence_lang' => $lang],
            ['sentence_id' => $sentence_id]
        );
    }


    /**
     * Log contributions related to sentences.
     *
     * @param int $sentenceId   Id of the sentence.
     * @param int $sentenceLang Languuage of the sentence.
     * @param int $action       Action performed ('insert', 'delete', or 'update').
     *
     * @return void
     */
    public function saveSentenceContribution($id, $lang, $script, $text, $action)
    {
        $data = $this->newEntity([
            'id' => null,
            'sentence_id' => $id,
            'sentence_lang' => $lang,
            'script' => $script,
            'text' => $text,
            'user_id' => CurrentUser::get('id'),
            'datetime' => date("Y-m-d H:i:s"),
            'type' => 'sentence',
            'action' => $action
        ]);

        $this->save($data);
    }


    /**
     * Log contributions related to links.
     *
     * @param int $sentenceId    Id of the sentence.
     * @param int $translationId Id of the translation.
     * @param int $action        Action performed ('insert' or 'delete').
     *
     * @return void
     */
    public function saveLinkContribution($sentenceId, $translationId, $action)
    {
        $data = $this->newEntity([
            'id' => null,
            'sentence_id' => $sentenceId,
            'translation_id' => $translationId,
            'user_id' => CurrentUser::get('id'),
            'datetime' => date("Y-m-d H:i:s"),
            'type' => 'link',
            'action' => $action
        ]);
        $this->save($data);
    }

    public function excludeBots($query)
    {
        $botsIds = Configure::read('Bots.userIds');

        if (!empty($botsIds)) {
            return $query->where(['user_id NOT IN' => $botsIds]);
        }

        return $query;
    }

    public function getOriginalCreatorOf($sentenceId)
    {
        $log = $this->find()
            ->where([
                'sentence_id' => $sentenceId,
                'action' => 'insert',
                'type' => 'sentence',
            ])
            ->order(['datetime' => 'DESC'])
            ->first();

        if ($log) {
            return $log->user_id;
        } else {
            return false;
        }
    }
}
