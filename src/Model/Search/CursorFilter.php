<?php

namespace App\Model\Search;

use App\Model\Exception\InvalidValueException;

class CursorFilter extends BaseSearchFilter {

    use NeedsSearchRefTrait;

    public function anyOf(array $values) {
        foreach ($values as $value) {
            if (!is_numeric($value)) {
                throw new InvalidValueException("'$value' is not numeric");
            }
        }
        return parent::anyOf($values);
    }

    public function not() {
        throw new \App\Model\Exception\InvalidNotOperatorException();
    }

    public function and() {
        throw new \App\Model\Exception\InvalidAndOperatorException();
    }

    protected function _compile() {
        if (is_null($this->search)) {
            throw new \RuntimeException("Precondition failed: setSearch() was not called first");
        }

        $orderbys = $this->search->getInternalSortOrder();
        if (count($orderbys) == 0) {
            throw new \App\Model\Exception\InvalidFilterUsageException('No sort order defined');
        }

        $values = $this->filters[0];
        array_shift($values);
        if (count($orderbys) != count($values)) {
            throw new InvalidValueException('Expected '.count($orderbys).' value(s), got '.count($values).' instead');
        }

        if (count($orderbys) == 2) {
            list( list($key1, $isAsc1), list($key2, $isAsc2) ) = $orderbys;
            if ($key1 == '@rank') {
                $key1 = 'WEIGHT()';
            }
            list($val1, $val2) = $values;
            $op1 = $isAsc1 ? '>=' : '<=';
            $op2 = $isAsc2 ? '<=' : '>=';
            $keyset = "($key1 $op1 $val1 AND NOT ($key1 = $val1 AND $key2 $op2 $val2))";
            return [
                ['keyset', $keyset],
            ];
        } else {
            throw new \RuntimeException(self::class." does not support ".count($orderbys)." order field(s)");
        }
    }
}
