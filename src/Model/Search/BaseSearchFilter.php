<?php

namespace App\Model\Search;

abstract class BaseSearchFilter {
    protected $filters = [];
    protected $current = 0;
    protected $exclude = false;

    public static function getName() {
        // assumes this class is in a namespace
        $class = get_called_class();
        $pos = strrpos($class, "\\") + 1;
        return substr($class, $pos);
    }

    public function getAlias() {
        return self::getName();
    }

    public function getAllValues() {
        $values = [];
        foreach ($this->filters as $filter) {
            array_shift($filter);
            foreach ($filter as $value) {
                $values[$value] = null;
            }
        }
        return array_keys($values);
    }

    protected function _join(string $op, array $exprs, bool $negate = false) {
        $expr = implode(" $op ", $exprs);
        if (count($exprs) > 1 || ($negate && $exprs[0][0] != '(')) {
            $expr = "($expr)";
        }
        if ($negate) {
            $expr = "not $expr";
        }
        return $expr;
    }

    public function and() {
        $this->current++;
        $this->exclude = false;
        return $this;
    }

    public function not() {
        $this->exclude = true;
        return $this;
    }

    public function anyOf(array $values) {
        array_unshift($values, $this->exclude);
        $this->filters[$this->current] = $values;
        return $this;
    }

    abstract public function compile();
}
