<?php

namespace App\Model\Search;

trait FiltersCollectionTrait {

    protected $filters = [];

    public function getFilters() {
        return $this->filters;
    }

    public function getFilter($class, $index = '') {
        return $this->filters[ $class::getName($index) ] ?? null;
    }

    public function setFilter($filter) {
        $this->filters[ $filter->getAlias() ] = $filter;
        return $this;
    }

    public function unsetFilter($class, $index = '') {
        unset($this->filters[ $class::getName($index) ]);
    }
}
