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
 * @category Contributions
 * @package  Models
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class ContributionsStats extends AppModel
{
    /**
     * Returns number of contributions for each day. We only count the number of new
     * sentences, not the number of modifications.
     *
     * @return array
     */
    public function getActivityTimelineStatistics(
        $year = null, $month = null, $lang = null
    ) {
        if ($year == null || $month == null) {

            $startDate = date('Y-m');

        } else {

            $startTimestamp = mktime(0, 0, 0, intval($month), 1, intval($year));
            $endTimestamp = mktime(0, 0, 0, intval($month)+1, 1, intval($year));
            $startDate = date('Y-m-d', $startTimestamp);
            $endDate = date('Y-m-d', $endTimestamp);

        }

        return $this->find(
            'all',
            array(
                'fields' => array(
                    'lang',
                    'sentences',
                    'date',
                ),
                'conditions' => array(
                    'date >=' => $startDate,
                    'date <' => $endDate,
                    'type' => 'sentence',
                    'action' => 'insert',
                    'lang' => $lang
                ),
                'order' => 'date, sentences DESC'
            )
        );
    }
}