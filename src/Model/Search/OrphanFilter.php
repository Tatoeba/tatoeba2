<?php

namespace App\Model\Search;

class OrphanFilter extends BoolFilter {

    protected $valueForTrue = 0;

    protected function getAttributeName() {
        return 'user_id';
    }
}
