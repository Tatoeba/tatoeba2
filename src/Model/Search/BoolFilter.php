<?php

namespace App\Model\Search;

abstract class BoolFilter extends SearchFilter {
    protected $valueForTrue = 1;

    protected abstract function getAttributeName();

    public function __construct(bool $value = true) {
        $this->exclude = !$value;
        $this->anyOf([$this->valueForTrue]);
    }
}
