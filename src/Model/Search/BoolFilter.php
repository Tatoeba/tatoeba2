<?php

namespace App\Model\Search;

abstract class BoolFilter extends SearchFilter {
    protected $valueForTrue = 1;

    protected abstract function getAttributeName();

    public function and() {
        throw new \App\Model\Exception\InvalidAndOperatorException($this);
    }

    protected function calcFilter() {
        $this->anyOf([$this->valueForTrue]);
    }

    protected function _compile() {
        $this->calcFilter();
        return parent::_compile();
    }
}
