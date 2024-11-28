<?php

namespace App\Model\Search;

abstract class BoolFilter extends SearchFilter {
    protected $valueForTrue = 1;

    protected abstract function getAttributeName();

    public function __construct(bool $value = true) {
        if (!$value) {
            $this->not();
        }
    }

    protected function calcFilter() {
        $this->anyOf([$this->valueForTrue]);
    }

    protected function _compile() {
        $this->calcFilter();
        return parent::_compile();
    }
}
