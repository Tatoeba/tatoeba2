<?php

namespace App\Model\Search;

use App\Model\Exception\InvalidValueException;
use Cake\Database\Expression\FunctionExpression;
use Cake\Datasource\ModelAwareTrait;
use Cake\Utility\Hash;

class ListFilter extends SearchFilter {
    use ModelAwareTrait;

    private $currentUserId;

    protected function getAttributeName() {
        return 'lists_id';
    }

    public function __construct($currentUserId = null) {
        $this->currentUserId = $currentUserId;
        $this->setInvalidValueHandler(function($invalidValue) {
            throw new InvalidValueException("No such list id: '$invalidValue'");
        });
    }

    public function anyOf(array $values) {
        foreach ($values as $value) {
            if (!is_numeric($value)) {
                throw new InvalidValueException("Invalid list id: '$value'");
            }
        }
        return parent::anyOf($values);
    }

    public function getValuesMap() {
        $listIds = $this->getAllValues();
        if ($listIds) {
            $this->loadModel('SentencesLists');
            $order = new FunctionExpression(
                'FIND_IN_SET',
                ['SentencesLists.id' => 'literal', implode(',', $listIds)]
            );
            $result = $this->SentencesLists
                ->find('searchableBy', ['user_id' => $this->currentUserId])
                ->where(['id IN' => $listIds])
                ->select(['id'])
                ->order($order)
                ->enableHydration(false)
                ->toList();
            return Hash::combine($result, '{n}.id', '{n}.id');
        } else {
            return [];
        }
    }
}
