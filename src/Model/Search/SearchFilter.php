<?php

namespace App\Model\Search;

abstract class SearchFilter extends BaseSearchFilter {
    private $cache;
    private $invalidValueHandler;

    protected abstract function getAttributeName();

    public function setInvalidValueHandler(\Closure $handler) {
        $this->invalidValueHandler = $handler;
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
