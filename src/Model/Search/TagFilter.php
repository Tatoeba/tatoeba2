<?php

namespace App\Model\Search;

use App\Model\Exception\InvalidValueException;
use App\Utility\MappedKeysArray;
use Cake\Database\Expression\FunctionExpression;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Hash;

class TagFilter extends SearchFilter {
    use LocatorAwareTrait;

    protected function getAttributeName() {
        return 'tags_id';
    }

    public function __construct(string $name = null) {
        parent::__construct($name);
        $this->setInvalidValueHandler(function($invalidValue) {
            throw new InvalidValueException($this, "No such tag: '$invalidValue'");
        });
    }

    public function getValuesMap() {
        $tags = $this->getAllValues();
        if ($tags) {
            $order = new FunctionExpression(
                'FIND_IN_SET',
                ['Tags.name' => 'literal', implode(',', $tags)]
            );
            $result = $this->fetchTable('Tags')->find()
                ->where(['name IN' => $tags])
                ->select(['id', 'name'])
                ->order($order)
                ->enableHydration(false)
                ->all()
                ->toList();
            $mapper = function ($key) {
                return is_string($key) ? mb_strtolower($key) : $key;
            };
            return new MappedKeysArray($mapper, Hash::combine($result, '{n}.name', '{n}.id'));
        } else {
            return [];
        }
    }
}
