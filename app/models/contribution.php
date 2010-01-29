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
            array('conditions' => array('Contribution.user_id' => $userId))
        );
    }

    /**
     * Get the top contributors (those who have the highest score when calculating
     * number of sentences added + number of modifications).
     *
     * @param int $limit Number of top contributors.
     *
     * @return array
     */
    public function getTopContributors($limit)
    {
        $result = $this->find(
            'all',
            array(
                'order' => 'total DESC',
                'fields' => array(
                    'COUNT(Contribution.id) AS total',
                    'User.username',
                    'User.group_id'
                ),
                'group' => 'Contribution.user_id',
                'conditions' => array (
                    'Contribution.user_id !=' => null,
                    'Contribution.type' => 'sentence',
                    'User.group_id <' => 5
                ),
                'limit' => $limit ,
                'contains' => array (
                    'User' => array (
                        'fields' => array( 'User.username', 'User.group_id'),
                    ),
                ) 
            )
        );
        return $result; 
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
                'contains' => array(
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
        if (strlen($lang) != 3 OR !is_numeric($limit)) {
            return array();
        }
        $conditions = array('Contribution.type' => 'sentence');

        if ($lang != 'und') {
            $conditions['Sentence.lang'] = $lang ;
        }

        $query ="
            SELECT 
                `Contribution`.`text`,
                `Contribution`.`translation_id`,
                `Contribution`.`sentence_id`,
                `Contribution`.`action`,
                `Contribution`.`id`,
                `Contribution`.`datetime`,
                `User`.`username`,
                `User`.`id`,
                `Sentence`.`lang`
            FROM `contributions` AS `Contribution` 
                INNER JOIN `sentences` AS `Sentence`
                    ON (`Contribution`.`sentence_id` = `Sentence`.`id`
        ";
        if ($lang != 'und') {
            $query .= "AND `Sentence`.`lang` = '$lang'";
        }
        $query.="
                )
                INNER JOIN `users` AS `User`
                    ON (`Contribution`.`user_id` = `User`.`id`)
            WHERE ";
        if ($lang != 'und') {
            $query .=  "`Sentence`.`lang` = '$lang' AND";
        } 

        $query.="
                `Contribution`.`type` = 'sentence'
            ORDER BY `Contribution`.`datetime` DESC 
            LIMIT $limit; 
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
        return $this->find('all', $query);
    }    
    
    
    /**
     * Returns number of contributions for each day. We only count the number of new
     * sentences, not the number of modifications.
     *
     * @return array
     */
    public function getActivityTimelineStatistics()
    {
        return $this->find(
            'all', 
            array(
                'fields' => array(
                    'Contribution.datetime'
                    , 'COUNT(*) as total'
                    , 'date_format(datetime,\'%b %D %Y\') as day'
                ),
                'conditions' => array(
                    'Contribution.datetime > \'2008-01-01 00:00:00\''
                    , 'Contribution.translation_id' => null
                    , 'Contribution.action' => 'insert'
                ),
                'group' => array('day'),
                'order' => 'Contribution.datetime DESC',
                'contain' => array()
            )
        );
    }
}
?>
