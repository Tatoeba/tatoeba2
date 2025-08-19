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

use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Behavior;
use Cake\ORM\Query;

class LimitResultsBehavior extends Behavior
{
    private function getNeededAssociations(Query $query) {
        $fields = [];

        $clause = $query->clause('where');
        if ($clause) {
            $clause->traverse(function($c) use (&$fields) {
                $hasFieldTrait = in_array(
                    'Cake\Database\Expression\FieldTrait',
                    class_uses($c)
                );
                if ($hasFieldTrait) {
                    $fields[] = $c->getField();
                }
            });
        }

        return array_map(function ($key) {
            $splitPos = strpos($key, '.');
            if ($splitPos) {
                return substr($key, 0, $splitPos);
            } else {
                return '';
            }
        }, $fields);
    }

    /**
     * Calculate minimal associations for a query
     *
     * Helper function for findLatest which filters the associations to
     * load for the given query. Only the associations mentioned in the 'where'
     * part are necessary for calculating the lowest id we need.
     *
     * @param array $query The query to calculate from
     *
     * @return array
     **/
    private function getMinimalContain(Query $query) {
        $neededAssociations = $this->getNeededAssociations($query);
        $contain = $query->getEagerLoader()->getContain();
        return array_filter(
            $contain,
            function ($key) use ($neededAssociations) {
                return in_array($key, $neededAssociations);
            },
            ARRAY_FILTER_USE_KEY);
    }

    /**
     * Removes any LEFT JOIN clause to tables that are not mentioned
     * in the 'where' part of the query.
     */
    private function removeLeftJoins(Query $query) {
        $neededAssociations = $this->getNeededAssociations($query);
        foreach ($query->clause('join') as $name => $join) {
            if ($join['type'] == 'LEFT'
                && !in_array($join['alias'], $neededAssociations)) {
                $query->removeJoin($name);
            }
        }
    }

    /**
     * Retrieve field and direction used in ORDER BY clause of a query.
     *
     * Helper function for findLatest.
     * Only supports ORDER BY on a single field.
     *
     * @param array $query The query to retrieve from.
     *
     * @return array Field name and direction.
     **/
    private function getOrderValues(Query $query) {
        $direction = null;
        $orderField = null;

        $order = $query->clause('order');
        if ($order) {
            $order->iterateParts(function($v, $k) use (&$direction, &$orderField) {
                if (is_numeric($k)) {
                    $orderField = $v;
                } else {
                    $orderField = $k;
                    $direction = strtolower($v);
                }
                return $v;
            });
        }

        return [$orderField, $direction];
    }

    /**
     * This custom finder is used to add a strong and efficient
     * limit to a query by adding a WHERE _field_ > n clause,
     * _field_ being the field used in ORDER BY, typically it is id.
     * It is used as a safeguard for paginated results because
     * browsing pages of high numbers results in very poor
     * performance.
     */
    public function findLatest(Query $query, array $options) {
        list($orderField, $direction) = $this->getOrderValues($query);
        if (!$orderField) {
            // We cannot limit the results without a sort order. If there is
            // no sort order, it means the programmer forgot to set one, or
            // the client is playing tricks requesting a non-whitelisted
            // order. Both are good reasons to bail out.
            throw new BadRequestException("Invalid sort order");
        }

        $alias = $query->repository()->getAlias();

        $contain = $this->getMinimalContain($query);

        $internalQuery = clone $query;
        $this->removeLeftJoins($internalQuery);
        $lastValue = $internalQuery
            ->find('list', ['valueField' => 'i'])
            ->select(['i' => $orderField], true)
            ->contain($contain, true)
            ->limit($options['maxResults'])
            ->offset(null)
            ->group([], true)
            ->last();

        if ($lastValue) {
            $cmp = $direction == 'desc' ? '>=' : '<=';
            $query->where(["$orderField $cmp" => $lastValue]);
        }
        return $query;
    }
}
