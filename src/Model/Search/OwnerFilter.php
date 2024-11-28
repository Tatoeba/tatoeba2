<?php

namespace App\Model\Search;

use App\Model\Exception\InvalidValueException;
use Cake\Database\Expression\FunctionExpression;
use Cake\Datasource\ModelAwareTrait;
use Cake\Utility\Hash;

class OwnerFilter extends SearchFilter {
    use ModelAwareTrait;

    protected function getAttributeName() {
        return 'user_id';
    }

    public function __construct() {
        $this->setInvalidValueHandler(function($invalidValue) {
            throw new InvalidValueException("No such owner: '$invalidValue'");
        });
    }

    public function and() {
        throw new \App\Model\Exception\InvalidAndOperatorException();
    }

    protected function handleInvalidValue($invalidValue) {
        if (is_null($invalidValue)) {
            return 0;
        } else {
            return $this->runInvalidValueHandler($invalidValue);
        }
    }

    public function getValuesMap() {
        $users = $this->getAllValues();
        if ($users) {
            $this->loadModel('Users');
            $order = new FunctionExpression(
                'FIND_IN_SET',
                ['Users.username' => 'literal', implode(',', $users)]
            );
            $result = $this->Users->find()
                ->where(['username IN' => $users])
                ->select(['id', 'username'])
                ->order($order)
                ->enableHydration(false)
                ->toList();
            return Hash::combine($result, '{n}.username', '{n}.id');
        } else {
            return [];
        }
    }
}
