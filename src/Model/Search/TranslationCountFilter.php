<?php

namespace App\Model\Search;

use App\Model\Exception\InvalidValueException;

class TranslationCountFilter extends BaseSearchFilter {
    public function anyOf(array $values) {
        if (count($values) == 1 && ($values[0] === 0 || $values[0] === '0')) {
            return parent::anyOf($values);
        } else {
            throw new InvalidValueException('Only a single value of 0 is allowed');
        }
    }

    protected function _compile() {
        if (count($this->filters)) {
            $values = $this->filters[0];
            $exclude = array_shift($values);
            $expr = "(length(trans) = {$values[0]})";
            if ($exclude) {
                $expr = "not $expr";
            }
            return [[self::getName(), $expr]];
        } else {
            return [];
        }
    }
}
