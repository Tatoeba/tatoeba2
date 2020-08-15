<?php
/**
 * Tatoeba Project, free collaborative creation of languages corpuses project
 * Copyright (C) 2020 Tatoeba Project
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
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Query;

class LimitResultsBehavior extends Behavior
{
    /**
     * Calculate minimal associations for a query
     *
     * Helper function for paginateLatest which filters the associations to
     * load for the given query. Only the associations mentioned in the 'select'
     * and 'order' part are necessary for calculating the lowest id we need.
     *
     * @param array $query The query to calculate from
     *
     * @return array
     **/
    private function getMinimalContain(Query $query) {
        $fields = [];
        foreach (['where', 'order'] as $clause) {
            $clause = $query->clause($clause);
            if ($clause) {
                $clause->traverse(function($c) use (&$fields) {
                    $fields[] = $c->getField();
                });
            }
        }
        $neededAssociations = array_map(function ($key) {
            $splitPos = strpos($key, '.');
            if ($splitPos) {
                return substr($key, 0, $splitPos);
            } else {
                return '';
            }
        }, $fields);

        $contain = $query->getEagerLoader()->getContain();
        return array_filter(
            $contain,
            function ($key) use ($neededAssociations) {
                return in_array($key, $neededAssociations);
            },
            ARRAY_FILTER_USE_KEY);
    }

    /**
     * This custom finder is used to add a strong and efficient
     * limit to a query by adding a WHERE id > n clause.
     * It is used as a safeguard for paginated results because
     * browsing pages of high numbers results in very poor
     * performance.
     */
    public function findLatest(Query $query, array $options) {
        $alias = $query->repository()->getAlias();
        $additionalOrder = [$alias . '.id' => 'DESC'];

        $contain = $this->getMinimalContain($query);

        $internalQuery = clone $query;
        $lastId = $internalQuery->find('list')
            ->select([$alias . '.id'], true)
            ->contain($contain, true)
            ->order($additionalOrder)
            ->limit($options['maxResults'])
            ->last();

        return $query->where([$alias . '.id >=' => $lastId]);
    }
}
