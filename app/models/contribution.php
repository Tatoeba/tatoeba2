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
 * Model for contributions.
 *
 * @category Contributions
 * @package  Models
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class Contribution extends AppModel
{
    public $actsAs = array("Containable");
    public $belongsTo = array('Sentence', 'User');

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
                'contain' => array(),
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
        $result = $this->find(
            'all',
            array(
                'fields' => array(
                    'Contribution.sentence_lang',
                    'Contribution.text',
                    'Contribution.translation_id',
                    'Contribution.action',
                    'Contribution.id',
                    'Contribution.datetime',
                    'User.username',
                    'User.id'
                ),
                'conditions' => array(
                    'Contribution.sentence_id' => $sentenceId
                ),
                'contain' => array(
                    'User'=> array(
                        'fields' => array('User.username','User.id')
                    ),
                )
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
    public function getLastContributions($limit,$lang = 'und')
    {
        // we sanitize, really important here as we forge our own query
        $limit = Sanitize::paranoid($limit);
        $lang = Sanitize::paranoid($lang);

        if (strlen($lang) != 3 || !is_numeric($limit)) {
            return array();
        }

        $conditions = array('Contribution.type' => 'sentence');

        $table = "contributions";
        if ($lang == 'und'|| empty($lang)) {
            $table = "last_contributions";
        }

        $query ="
            SELECT
                `Contribution`.`text`,
                `Contribution`.`action`,
                `Contribution`.`id`,
                `Contribution`.`sentence_id`,
                `Contribution`.`datetime`,
                `Contribution`.`sentence_lang`,
                `User`.`username`,
                `User`.`id`
            FROM `$table` AS `Contribution`
        ";
        $query.="
                INNER JOIN `users` AS `User`
                    ON (`Contribution`.`user_id` = `User`.`id`)
                 WHERE ";
        if ($lang != 'und') {
            $query .=  "`Contribution`.`sentence_lang` = '$lang' AND";
        }

        $query.="
                `Contribution`.`type` = 'sentence'
            ORDER BY `Contribution`.`id` DESC
            LIMIT $limit
        ";

        return  $this->query($query);
    }

    /**
     * Returns number of contributions for each member, ordered from the highest
     * contributor to the lowest.
     *
     * @return array
     */
    public function getUsersStatistics()
    {
        $query = array(
            'fields' => array(
                'Contribution.user_id', 'User.id', 'User.username'
                , 'User.since', 'User.group_id', 'COUNT(*) as total'
            ),
            'conditions' => array(
                'User.id !=' => null
                , 'Contribution.type' => 'sentence'
            ),
            'group' => array('Contribution.user_id'),
            'order' => 'total DESC',
            'contain' => array(
                'User' => array(
                    'fields' => array('username','id')
                )
            )
        );
        return array();//$this->find('all', $query);
    }


    /**
     * Returns number of contributions for each day. We only count the number of new
     * sentences, not the number of modifications.
     *
     * @return array
     */
    public function getActivityTimelineStatistics($year = null, $month = null)
    {
        if ($year == null || $month == null) {

            $startDate = date('Y-m');
            $numDays = date('t');

        } else {

            $startTimestamp = mktime(0, 0, 0, intval($month), 1, intval($year));
            $endTimestamp = mktime(0, 0, 0, intval($month)+1, 1, intval($year));
            $startDate = date('Y-m', $startTimestamp);
            $endDate = date('Y-m', $endTimestamp);

        }

        return $this->find(
            'all',
            array(
                'fields' => array(
                    'COUNT(*) as total',
                    'date_format(datetime,\'%b %D %Y\') as day',
                ),
                'conditions' => array(
                    'Contribution.datetime > \''.$startDate.'\'',
                    'Contribution.datetime < \''.$endDate.'\'',
                    'Contribution.type' => 'sentence',
                    'Contribution.action' => 'insert',
                ),
                'group' => array('day'),
                'order' => 'Contribution.id DESC',
                'contain' => array()
            )
        );
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
                    'Contribution.action' => 'insert'
                ),
                'contain' => array()
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
    public function saveSentenceContribution($id, $lang, $text, $action)
    {
        $data = array(
            'id' => null,
            'sentence_id' => $id,
            'sentence_lang' => $lang,
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

}
?>
