<?php
namespace App\ORM\Association\Loader;

use Cake\Datasource\QueryInterface;
use Cake\ORM\Association;
use Cake\ORM\Association\Loader\SelectWithPivotLoader;

/**
 * Implements the logic for loading an association using a SELECT query and two pivot tables
 */
class SelectWithDoublePivotLoader extends SelectWithPivotLoader
{
    protected function _buildQuery($options)
    {
        $key = $this->_linkField($options);
        $filter = $options['keys'];
        $useSubquery = $options['strategy'] === Association::STRATEGY_SUBQUERY;
        $finder = $this->finder;

        if (!isset($options['fields'])) {
            $options['fields'] = [];
        }

        /** @var \Cake\ORM\Query $query */
        $query = $finder();
        if (isset($options['finder'])) {
            list($finderName, $opts) = $this->_extractFinder($options['finder']);
            $query = $query->find($finderName, $opts);
        }

        $fetchQuery = $query
            ->select($options['fields'])
            ->where($options['conditions'])
            ->eagerLoaded(true)
            ->enableHydration($options['query']->isHydrationEnabled());

        $name = $this->junctionAssociationName;
        $targetForeignKey = $this->junctionAssoc->getForeignKey();
        $secondPivotTableAlias = $this->junctionAssoc->getTarget()->getAlias() . '_2';
        $subQuery = $this->junctionAssoc->getTarget()->find()
            ->select([
                $this->foreignKey => $key,
                '_modal_key' => "IF($key = $secondPivotTableAlias.$targetForeignKey, $secondPivotTableAlias.{$this->foreignKey}, $secondPivotTableAlias.$targetForeignKey)",
                'is_direct' => "MAX($key = $secondPivotTableAlias.$targetForeignKey)"])
            ->join([$secondPivotTableAlias => [
                'table' => $this->junctionAssoc->getTarget()->getTable(),
                'conditions' => ["$name.$targetForeignKey = $secondPivotTableAlias.{$this->foreignKey}"],
                'type' => QueryInterface::JOIN_TYPE_INNER,
            ]])
            ->group([$this->foreignKey, '_modal_key']);

        if ($useSubquery) {
            $filter = $this->_buildSubquery($options['query']);
            $subQuery = $this->_addFilteringJoin($subQuery, $key, $filter);
        } else {
            $subQuery = $this->_addFilteringCondition($subQuery, $key, $filter);
        }

        $fetchQuery->join([$name => [
            'table' => $subQuery,
            'conditions' => ["{$this->alias}.{$this->bindingKey} = $name._modal_key"],
            'type' => QueryInterface::JOIN_TYPE_INNER,
        ]]);
        if ($fetchQuery->isAutoFieldsEnabled() === null) {
            $fetchQuery->enableAutoFields($fetchQuery->clause('select') === []);
        }
        $fetchQuery->select([
            "{$this->alias}__is_direct" => "$name.is_direct",
            "{$this->alias}__{$this->foreignKey}" => "$name.{$this->foreignKey}"
        ]);
        $fetchQuery->getTypeMap()->addDefaults([
            "{$this->alias}__is_direct" => 'boolean',
            "is_direct" => 'boolean',
            "{$this->alias}__{$this->foreignKey}" => 'integer',
        ]);

        if (!empty($options['sort'])) {
            $fetchQuery->order($options['sort']);
        }

        if (!empty($options['contain'])) {
            $fetchQuery->contain($options['contain']);
        }

        if (!empty($options['queryBuilder'])) {
            $fetchQuery = $options['queryBuilder']($fetchQuery);
        }

        return $fetchQuery;
    }

    protected function _buildResultMap($fetchQuery, $options)
    {
        $resultMap = [];
        $key = (array)$options['foreignKey'];

        foreach ($fetchQuery->all() as $result) {
            $values = [];
            foreach ($key as $k) {
                $values[] = $result[$k];
            }
            $resultMap[implode(';', $values)][] = $result;
        }

        return $resultMap;
    }
}
