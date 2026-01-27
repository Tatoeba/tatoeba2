<?php

namespace App\Model\Search;

use App\Model\Search\TranslationFilterGroup;

trait FiltersCollectionTrait {

    protected $filters = [];

    public function getFilters() {
        return $this->filters;
    }

    public function getFilter($class, ...$args) {
        $key = $class::getDefaultName(...$args);
        return $this->filters[$key] ?? null;
    }

    public function setFilter($filter) {
        $this->filters[ $filter->getName() ] = $filter;
        return $this;
    }

    public function unsetFilter($class, ...$args) {
        $key = $class::getDefaultName(...$args);
        unset($this->filters[$key]);
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
