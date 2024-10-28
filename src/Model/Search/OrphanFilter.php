<?php

namespace App\Model\Search;

class OrphanFilter extends BoolFilter {

    public function __construct(bool $value) {
        parent::__construct($value);
        $this->anyOf([0]);
    }

    protected function getAttributeName() {
        return 'user_id';
    }
}
