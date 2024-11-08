<?php

namespace App\Model\Search;

class IsOrphanFilter extends BoolFilter {

    protected $valueForTrue = 0;

    protected function getAttributeName() {
        return 'user_id';
    }
}
