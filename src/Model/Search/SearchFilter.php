<?php

namespace App\Model\Search;

abstract class SearchFilter extends BaseSearchFilter {
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

    protected function getMappedFilters() {
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
                $output[] = [$exclude, $values];
            }
        }
        return $output;
    }

    protected function _compile() {
        return array_map(
            function ($args) {
                list ($exclude, $mappedValues) = $args;
                return [$this->getAttributeName(), $mappedValues, $exclude];
            },
            $this->getMappedFilters()
        );
    }
}
