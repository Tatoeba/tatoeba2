<?php

namespace App\Model\Search;

class IsUnapprovedFilter extends BoolFilter {

    // See the indexation SQL request for the value 127
    protected $valueForTrue = 127;

    protected function getAttributeName() {
        return 'ucorrectness';
    }
}
