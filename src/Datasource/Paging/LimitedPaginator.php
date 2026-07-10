<?php
declare(strict_types=1);

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
namespace App\Datasource\Paging;

use Cake\Datasource\QueryInterface;
use Cake\Datasource\Paging\NumericPaginator;
use Cake\Datasource\RepositoryInterface;
use Cake\Datasource\ResultSetInterface;

/**
 * A paginator used to add a strong and efficient limit to paginated queries.
 * It is used as a safeguard for paginated results when browsing high-numbered
 * pages, which result in very poor performance. The poor performance comes
 * from the `OFFSET n` clause with n very high, typically greater than 10000.
 */
class LimitedPaginator extends NumericPaginator
{
    public function __construct()
    {
        $this->_defaultConfig['maxResults'] = 1000;
    }

    private function getNeededAssociations(QueryInterface $query) {
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
    private function getMinimalContain(QueryInterface $query) {
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
    private function removeLeftJoins(QueryInterface $query) {
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
    private function getOrderValues(QueryInterface $query) {
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
     * Adds a WHERE _field_ > n clause to $paginationQuery, _field_ being
     * the ORDER BY field of $baseQuery, typically it is id.
     * Also makes sure any existing OFFSET clause is clamped to $maxResults.
     *
     * @param int $maxResults Maximum number of results $paginationQuery should return.
     * @param \Cake\Datasource\QueryInterface $paginationQuery Query to apply limit to.
     * @param \Cake\Datasource\QueryInterface $baseQuery Query to calculate limit from.
     * @return \Cake\Datasource\QueryInterface Modified $paginationQuery.
     */
    public function applyLimit(int $maxResults, QueryInterface $paginationQuery, QueryInterface $baseQuery): QueryInterface
    {
        list($orderField, $direction) = $this->getOrderValues($baseQuery);
        if (!$orderField) {
            // We cannot limit the results without a sort order. If there is
            // no sort order, it means the programmer forgot to set one, or
            // the client is playing tricks requesting a non-whitelisted
            // order. Both are good reasons to bail out.
            throw new \RuntimeException("Invalid sort order");
        }

        $contain = $this->getMinimalContain($baseQuery);

        $internalQuery = clone $baseQuery;
        $this->removeLeftJoins($internalQuery);
        $lastValue = $internalQuery
            ->find('list', valueField: 'i')
            ->select([$baseQuery->getRepository()->getPrimaryKey(), 'i' => $orderField], true)
            ->contain($contain, true)
            ->offset($maxResults - 1)
            ->groupBy([], true)
            ->first();

        if ($lastValue) {
            $cmp = $direction == 'desc' ? '>=' : '<=';
            $paginationQuery->where(["$orderField $cmp" => $lastValue]);
        }

        // Prevent running a request having OFFSET n with n excessively high
        // just because the user asked for page 9999999
        $offset = min($maxResults, $paginationQuery->clause('offset'));
        $paginationQuery->offset($offset);

        return $paginationQuery;
    }

    /**
     * Get query for fetching paginated results while efficiently limiting
     * the total number of results using passed option 'maxResults'.
     *
     * @param \Cake\Datasource\RepositoryInterface $object Repository instance.
     * @param \Cake\Datasource\QueryInterface|null $query Query Instance.
     * @param array<string, mixed> $data Pagination data.
     * @return \Cake\Datasource\QueryInterface
     */
    protected function getQuery(RepositoryInterface $object, ?QueryInterface $query, array $data): QueryInterface
    {
        $query = parent::getQuery($object, $query, $data);

        $maxResults = $data['options']['maxResults'];

        return $this->applyLimit($maxResults, $query, $query);
    }
}
