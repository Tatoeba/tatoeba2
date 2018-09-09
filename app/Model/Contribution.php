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

App::uses('CakeEventManager', 'Event');

/**
 * Model for contributions.
 *
 * @category Contributions
 * @package  Models
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class Contribution extends AppModel implements CakeEventListener
{
    public $actsAs = array(
        "Containable"
    );
    public $belongsTo = array('Sentence', 'User');

    public function implementedEvents() {
        return array(
            'Model.Sentence.saved' => array('callable' => 'logSentence'),
        ) + parent::implementedEvents();
    }

    public function logSentence($event) {
        if (isset($event->data['data']['license'])) {
            $this->create();
            $this->save(array(
                'sentence_id' => $event->data['id'],
                'user_id' => CurrentUser::get('id'),
                'datetime' => date("Y-m-d H:i:s"),
                'ip' => CurrentUser::getIp(),
                'action' => $event->data['created'] ? 'insert' : 'update',
                'type' => 'license',
                'text' => $event->data['data']['license'],
            ));
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
        return $this->find(
            'count',
            array(
                'conditions' => array(
                    'Contribution.user_id' => $userId
                )
            )
        );
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
        $conditions = array(
            'Contribution.sentence_id' => $sentenceId,
        );
        if (!CurrentUser::isAdmin()) {
            $conditions['Contribution.type !='] = 'license';
        }

        $result = $this->find(
            'all',
            array(
                'fields' => array(
                    'Contribution.sentence_lang',
                    'Contribution.script',
                    'Contribution.text',
                    'Contribution.translation_id',
                    'Contribution.action',
                    'Contribution.id',
                    'Contribution.datetime',
                    'Contribution.type',
                    'User.username',
                    'User.id'
                ),
                'conditions' => $conditions,
                'contain' => array(
                    'User'=> array(
                        'fields' => array('User.username','User.id')
                    ),
                ),
                'order' => array('Contribution.datetime')
            )
        );
        return $result ;
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
        // we sanitize, really important here as we forge our own query
        $limit = Sanitize::paranoid($limit);
        $lang = Sanitize::paranoid($lang);

        if (!is_numeric($limit)) {
            return array();
        }

        $conditions = array('type' => 'sentence');

        if ($lang == 'und'|| empty($lang)) {
            $this->setSource('last_contributions');
        } else {
            $conditions['sentence_lang'] = $lang;
        }

        $conditions = $this->getQueryConditionsWithExcludedUsers($conditions);

        $contain = array(
            'User' => array(
                'fields' => array(
                    'id',
                    'username',
                    'image'
                )
            )
        );

        $results = $this->find(
            'all',
            array(
                'fields' => array(
                    'sentence_id',
                    'sentence_lang',
                    'script',
                    'text',
                    'datetime',
                    'action'
                ),
                'conditions' => $conditions,
                'order' => 'datetime DESC',
                'limit' => $limit,
                'contain' => $contain
            )
        );

        return $results;
    }

    /**
    * Return number of contributions for current day since midnight.
    *
    * @return int
    */

    public function getTodayContributions()
    {
        $currentDate = 'Contribution.datetime >'.'\''.date('Y-m-d').' 00:00:00\'';
        return $this->find(
            'count',
            array(
                'conditions' => array(
                    $currentDate,
                    'Contribution.translation_id' => null,
                    'Contribution.action' => 'insert',
                    'Contribution.type !=' => 'license'
                ),
            )
        );
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
            array(
                "sentence_lang" => "'$lang'"
            ),
            array(
                "sentence_id" => $sentence_id
            )
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
        $data = array(
            'id' => null,
            'sentence_id' => $id,
            'sentence_lang' => $lang,
            'script' => $script,
            'text' => $text,
            'user_id' => CurrentUser::get('id'),
            'datetime' => date("Y-m-d H:i:s"),
            'ip' => CurrentUser::getIp(),
            'type' => 'sentence',
            'action' => $action
        );

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
        $data = array(
            'id' => null,
            'sentence_id' => $sentenceId,
            'translation_id' => $translationId,
            'user_id' => CurrentUser::get('id'),
            'datetime' => date("Y-m-d H:i:s"),
            'ip' => CurrentUser::getIp(),
            'type' => 'link',
            'action' => $action
        );
        $this->save($data);
    }


    /**
     *
     *
     */
    public function getQueryConditionsWithExcludedUsers($conditions)
    {
        $botsIds = Configure::read('Bots.userIds');

        if (!isset($conditions)) {
            $conditions = array();
        }
        if (!empty($botsIds)) {
            $conditions['NOT'] = array('user_id' => $botsIds);
        }

        return $conditions;
    }


    public function getLastContributionOf($userId)
    {
        return $this->find(
            'all',
            array(
                'conditions' => array('user_id' => $userId),
                'fields' => array('ip', 'count(*) as count'),
                'group' => 'ip',
                'order' => 'count DESC',
                'limit' => 10
            )
        );
    }


    public function getOriginalCreatorOf($sentenceId)
    {
        $log = $this->find('first', array(
            'fields' => array('user_id'),
            'conditions' => array(
                'sentence_id' => $sentenceId,
                'action' => 'insert',
                'type' => 'sentence',
            ),
            'order' => 'datetime',
        ));
        if ($log) {
            return $log['Contribution']['user_id'];
        } else {
            return false;
        }
    }
}
