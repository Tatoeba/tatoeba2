<?php

namespace App\Model\Search;

use App\Model\Exception\InvalidValueException;
use Cake\Database\Expression\FunctionExpression;
use Cake\Datasource\ModelAwareTrait;
use Cake\Utility\Hash;

class TagFilter extends SearchFilter {
    use ModelAwareTrait;

    protected function getAttributeName() {
        return 'tags_id';
    }

    public function __construct() {
        $this->setInvalidValueHandler(function($invalidValue) {
            throw new InvalidValueException("No such tag: '$invalidValue'");
        });
    }

    public function getValuesMap() {
        $tags = $this->getAllValues();
        if ($tags) {
            $this->loadModel('Tags');
            $order = new FunctionExpression(
                'FIND_IN_SET',
                ['Tags.name' => 'literal', implode(',', $tags)]
            );
            $result = $this->Tags->find()
                ->where(['name IN' => $tags])
                ->select(['id', 'name'])
                ->order($order)
                ->enableHydration(false)
                ->toList();
            return Hash::combine($result, '{n}.name', '{n}.id');
        } else {
            return [];
        }
    }
}
