<?php

namespace App\Model\Search;

use App\Model\Exception\InvalidValueException;

class OriginFilter extends BaseSearchFilter {
    const ORIGIN_UNKNOWN     = 'unknown';
    const ORIGIN_KNOWN       = 'known';
    const ORIGIN_ORIGINAL    = 'original';
    const ORIGIN_TRANSLATION = 'translation';
    const ORIGIN_ALL = [self::ORIGIN_UNKNOWN, self::ORIGIN_KNOWN, self::ORIGIN_ORIGINAL, self::ORIGIN_TRANSLATION];

    public function anyOf(array $values) {
        if (count($values) != 1) {
            throw new InvalidValueException("Only a single value is accepted");
        }
        if (!in_array($values[0], self::ORIGIN_ALL)) {
            throw new InvalidValueException("Value must be one of: ".implode(', ', self::ORIGIN_ALL));
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
        $output = [];
        foreach ($this->filters as $values) {
            array_shift($values);
            if (count($values) == 1) {
                if ($values[0] == self::ORIGIN_UNKNOWN) {
                    $output[] = ['origin_known', false];
                } elseif ($values[0] == self::ORIGIN_KNOWN) {
                    $output[] = ['origin_known', true];
                } else {
                    $output[] = ['origin_known', true];
                    if ($values[0] == self::ORIGIN_ORIGINAL) {
                        $output[] = ['is_original', true];
                    } elseif ($values[0] == self::ORIGIN_TRANSLATION) {
                        $output[] = ['is_original', false];
                    }
                }
            }
        }
        return $output;
    }
}
