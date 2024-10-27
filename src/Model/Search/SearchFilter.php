<?php

namespace App\Model\Search;

abstract class SearchFilter {
    private $filters = [];
    private $current = 0;
    private $exclude = false;
    private $cache;
    private $invalidValueHandler;

    protected abstract function getAttributeName();

    public static function getName() {
        // assumes this class is in a namespace
        $class = get_called_class();
        $pos = strrpos($class, "\\") + 1;
        return substr($class, $pos);
    }

    public function setInvalidValueHandler(\Closure $handler) {
        $this->invalidValueHandler = $handler;
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

    protected function runInvalidValueHandler($invalidValue) {
        if ($this->invalidValueHandler instanceof \Closure) {
            return ($this->invalidValueHandler)($invalidValue);
        }
    }

    protected function handleInvalidValue($invalidValue) {
        return $this->runInvalidValueHandler($invalidValue);
    }

    protected function getValuesMap() {
    }

    public function compile() {
        if (isset($this->cache)) {
            return $this->cache;
        }
        $output = [];
        $valuesMap = $this->getValuesMap();
        foreach ($this->filters as $values) {
            $exclude = array_shift($values);
            if (!is_null($valuesMap)) {
                $mapped = [];
                foreach ($values as $name) {
                    if (isset($valuesMap[$name])) {
                        $mapped[] = $valuesMap[$name];
                    } else {
                        $ret = $this->handleInvalidValue($name);
                        if (!is_null($ret)) {
                            $mapped[] = $ret;
                        }
                    }
                }
                $values = $mapped;
            }
            if (count($values) > 0) {
                $output[] = [$this->getAttributeName(), $values, $exclude];
            }
        }
        return $this->cache = $output;
    }
}
