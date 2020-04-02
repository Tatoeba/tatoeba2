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
use Cake\Database\Schema\TableSchema;

class ContributionsStatsTable extends Table
{
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('date', 'string');
        return $schema;
    }

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

        $conditions = [
            'date >=' => $startDate,
            'date <' => $endDate
        ];

        if ($lang) {
            $conditions['lang'] = $lang;
        } else {
            $conditions['lang IS'] = null;
        }

        $contributionsStats = $this->find()
            ->where($conditions)
            ->select([
                'lang',
                'sentences',
                'type',
                'action',
                'date',
            ])
            ->order([
                'date' => 'ASC',
                'type' => 'ASC'
            ])
            ->toList();

        // The number of "link" sentences is two times what we want
        foreach ($contributionsStats as $contributionsStat) {
            if ($contributionsStat->type === 'sentence' && $contributionsStat->action === 'insert') {
                $stats[$contributionsStat->date]['added'] = $contributionsStat->sentences;
            } elseif ($contributionsStat->type === 'sentence' && $contributionsStat->action === 'delete') {
                    $stats[$contributionsStat->date]['deleted'] = $contributionsStat->sentences;
            } elseif ($contributionsStat->type === 'link' && $contributionsStat->action === 'insert') {
                    $stats[$contributionsStat->date]['linked'] = floor($contributionsStat->sentences / 2);
            } elseif ($contributionsStat->type === 'link' && $contributionsStat->action === 'delete') {
                    $stats[$contributionsStat->date]['unlinked'] = floor($contributionsStat->sentences / 2);
            }
        }

        if (!isset($stats)) {
            return [];
        } else {
            foreach ($stats as $date => $stat) {
                $stats[$date]['total'] = array_sum($stats[$date]);
            }
            return $stats;
        }
    }
}
