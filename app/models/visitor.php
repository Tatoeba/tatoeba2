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
 * Model for visitors.
 *
 * @category Visitors
 * @package  Models
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class Visitor extends AppModel
{

    public $actsAs = array('Containable');

    public $primaryKey = 'ip';

    /**
     * Returns number of online visitors.
     *
     * @return int
     */
    public function numberOfOnlineVisitors()
    {
        // TODO typically something than can be handle with caching
        // as we don't really need to "store" them

        // delete users with timestamp higher than 5 minutes
        $timestamp_5min = time() - (60 * 5);
        $this->deleteAll(array('timestamp < ' . $timestamp_5min), false);

        // adding visitor to the list
        $currentUserIp = CurrentUser::getIp();

        $results = $this->find(
            'first',
            array(
                'fields' => 'ip',
                'conditions' => array(
                    'ip' => $currentUserIp
                )
            )
        );

        if ($results == null) {
            $data['Visitor']['timestamp'] = time();
            $data['Visitor']['ip'] = $currentUserIp;
            $this->save($data);
        }

        return $this->find('count');
    }

}
?>
