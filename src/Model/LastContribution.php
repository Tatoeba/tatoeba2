<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
namespace App\Model;

use App\Model\AppModel;


/**
 * Model for last contributions.
 *
 * @category Contributions
 * @package  Models
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class LastContribution extends AppModel
{
    public $belongsTo = array('Sentence', 'User');
    public $actsAs = array('Containable');

    /**
     * Get the top contributors from the latest contributions.
     *
     * @param int $limit Number of top contributors.
     *
     * @return array
     */
    public function getCurrentContributors()
    {
        $result = $this->find(
            'all',
            array(
                'order' => 'total DESC',
                'fields' => array(
                    'COUNT(LastContribution.id) AS total',
                    'User.username',
                    'User.group_id',
                    'User.image'
                ),
                'group' => 'LastContribution.user_id',
                'contain' => array (
                    'User' => array (
                        'fields' => array(
                            'username', 'group_id', 'image'
                        ),
                    ),
                )
            )
        );

        foreach ($result as $i=>$contributor) {
            $result[$i] = array (
                'numberOfContributions' => $contributor[0]['total'],
                'userName' => $contributor['User']['username'],
                'image' => $contributor['User']['image']
            );
        }

        return $result;
    }


    /**
     * [getTotalLastContributions description]
     * @param  [type] $contributors [description]
     * @return [type]               [description]
     */
    public function getTotal($contributors)
    {
        $total = 0;
        foreach ($contributors as $i=>$contributor) {
            $total += $contributor['numberOfContributions'];
        }

        return $total;
    }

}
