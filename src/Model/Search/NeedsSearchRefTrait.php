<?php

namespace App\Model\Search;

use App\Model\Search;

trait NeedsSearchRefTrait {
    private $search;

    public function setSearch(Search $search) {
        $this->search = $search;
        return $this;
    }
}
