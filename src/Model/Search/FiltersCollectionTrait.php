<?php

namespace App\Model\Search;

use App\Model\Search\TranslationFilterGroup;

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

    public function getTranslationFilters($index = '') {
        $filter = $this->getFilter(TranslationFilterGroup::class, $index);
        if ($filter) {
            return $filter;
        } else {
            // autocreate
            $filter = new TranslationFilterGroup($index);
            $this->setFilter($filter);
            return $filter;
        }
    }
}
