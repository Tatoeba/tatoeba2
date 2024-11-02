<?php

namespace App\Model\Search;

use App\Model\Exception\InvalidValueException;

class WordCountFilter extends BaseSearchFilter {
    private function _expr($sqlOp, $i) {
        return "text_len $sqlOp $i";
    }

    private function _parseRange($range) {
        $parts = explode('-', $range);
        if (count($parts) > 2) {
            throw new InvalidValueException("Invalid range: '$range'");
        }
        foreach ($parts as $part) {
            if (strlen($part) > 0 && !is_numeric($part)) {
                throw new InvalidValueException("Invalid number: '$part'");
            }
        }
        $ret = [];
        if (count($parts) == 2) {
            list($from, $to) = array_map(function($i) { return strlen($i) > 0 ? (int)$i : null; }, $parts);
            if (is_null($from) && is_null($to)) {
                throw new InvalidValueException("Invalid infinite range: '$range'");
            }
            if (!is_null($from)) {
                $ret[] = $this->_expr('>=', $from);
            }
            if (!is_null($to)) {
                $ret[] = $this->_expr('<=', $to);
            }
        } else { // if (count($parts) == 1) {
            $ret[] = $this->_expr('=', (int)$parts[0]);
        }
        return $ret;
    }

    public function compile(&$select = "*") {
        $exprs = array_filter(array_map(
            function ($set) {
                $exclude = array_shift($set);
                $set = array_map([$this, '_parseRange'], $set);
                if (count($set) > 0) {
                    $exprs = array_map(
                        function ($values) {
                            return $this->_join('and', $values);
                        },
                        $set
                    );
                    return $this->_join('or', $exprs, $exclude);
                }
            },
            $this->filters
        ));
        if (count($exprs) > 0) {
            $filterName = self::getName();
            $expr = $this->_join('and', $exprs);
            $select .= ", $expr as $filterName";
            return [[$filterName, 1]];
        } else {
            return [];
        }
    }
}
