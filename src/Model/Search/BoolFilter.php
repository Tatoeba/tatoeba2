<?php

namespace App\Model\Search;

class BoolFilter extends BaseSearchFilter {
    protected $valueForTrue = 1;

    public function __construct(bool $value) {
        $this->exclude = !$value;
        $this->anyOf([$this->valueForTrue]);
    }

    protected function _compile() {
        return [[$this->getAttributeName(), $this->getAllValues(), $this->exclude]];
    }
}
