<?php
namespace App\Shell;

use Cake\Core\Exception\Exception;

trait BatchOperationTrait {
    public $batchOperationSize = 1000;

    private function _orderCondition($nonUniqueField, $lastValue, $pKey, $lastId) {
        if ($nonUniqueField == $pKey) {
            return array("$pKey >" => $lastId);
        } else {
            return array('AND' => array(
                "$nonUniqueField >=" => $lastValue,
                array('OR' => array(
                    "$nonUniqueField >" => $lastValue,
                    array('AND' => array(
                        $nonUniqueField => $lastValue,
                        "$pKey >" => $lastId,
                    )),
                )),
            ));
        }
    }

    private function ensureHasField($entity, $field) {
        if (!$entity->has($field)) {
            throw new Exception("Field '$field' needs to be speficied in ->select() in the batch operation query");
        }
    }

    public function batchOperationNewORM($query, $operation) {
        if (!$query->clause('order')) {
            $table = $query->getRepository();
            $query = $query->order($table->getAlias().'.'.$table->getPrimaryKey());
        }
        $query = $query->limit($this->batchOperationSize);

        $order = [];
        $query->clause('order')->iterateParts(function($part) use (&$order) {
            $order[] = $part;
            return $part;
        });
        $order1 = $order[0];
        if (count($order) == 2) {
            $order2 = $order[1];
        } else {
            $order2 = $order1;
        }

        $o1field = explode('.', $order1)[1];
        $o2field = explode('.', $order2)[1];

        $baseQuery = clone $query;
        $proceeded = 0;
        $entities = array();
        do {
            $entities = $query->all();
            $args = func_get_args();
            array_splice($args, 0, 2, [$entities]);
            $proceeded += call_user_func_array($operation, $args);
            $lastEnt = $entities->last();
            if ($lastEnt) {
                $this->ensureHasField($lastEnt, $o1field);
                $lastValue1 = $lastEnt->$o1field;
                $this->ensureHasField($lastEnt, $o2field);
                $lastValue2 = $lastEnt->$o2field;
                $query = clone $baseQuery;
                $query->where($this->_orderCondition($order1, $lastValue1, $order2, $lastValue2));
            }
        } while (!$entities->isEmpty());
        return $proceeded;
    }

    protected function batchOperation($model, $operation, $options) {
        $this->loadModel($model);
        if (!isset($options['order'])) {
            $options['order'] = $this->{$model}->getAlias().'.'.$this->{$model}->getPrimaryKey();
        }
        if (is_string($options['order'])) {
            $options['order'] = array($options['order']);
        }
        $order1 = $options['order'][0];
        if (count($options['order']) == 2) {
            $order2 = $options['order'][1];
        } else {
            $order2 = $order1;
        }
        if (isset($options['fields'])) {
            foreach ($options['order'] as $field) {
                $options['fields'][] = $field;
            }
        }

        $o1field = explode('.', $order1)[1];
        $o2field = explode('.', $order2)[1];
        $proceeded = 0;
        $options = array_merge(
            array(
                'contain' => array(),
                'limit' => $this->batchOperationSize,
            ),
            $options
        );

        if (!isset($options['conditions'])) {
            $options['conditions'] = array();
        }
        $options['conditions'][] = array();
        end($options['conditions']);
        $conditionKey = key($options['conditions']);
        reset($options['conditions']);

        $data = array();
        do {
            $data = $this->{$model}->find('all', $options)->toList();
            $args = func_get_args();
            array_splice($args, 0, 3, array($data, $model));
            $proceeded += call_user_func_array(array($this, $operation), $args);
            $lastRow = end($data);
            if ($lastRow) {
                $lastValue1 = $lastRow->$o1field;
                $lastValue2 = $lastRow->$o2field;
                $options['conditions'][$conditionKey] = $this->_orderCondition($order1, $lastValue1, $order2, $lastValue2);
            }
        } while ($data);
        return $proceeded;
    }
}
