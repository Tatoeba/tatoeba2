<?php

namespace App\Model\Search;

class BoolFilter extends BaseSearchFilter {
    public function __construct(bool $value) {
        $this->exclude = !$value;
        $this->anyOf([1]);
    }

    public function compile() {
        return [[$this->getAttributeName(), $this->getAllValues(), $this->exclude]];
    }
}
