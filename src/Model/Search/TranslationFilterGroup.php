<?php

namespace App\Model\Search;

class TranslationFiltersCollection {
}

class TranslationFilterGroup extends BaseSearchFilter {
    private $id;

    public static function getName(string $id = '') {
        return "tf{$id}";
    }

    public function __construct(string $id = '') {
        $this->id = $id;
        $this->filters = new TranslationFiltersCollection();
    }

    public function setExclude(bool $exclude = true) {
        $this->exclude = $exclude;
        return $this;
    }

    public function setFilter($filter) {
        $this->filters->{$filter::getName()} = $filter;
        return $this;
    }

    public function getFilter($class) {
        return $this->filters->{$class::getName()} ?? null;
    }

    public function unsetFilter($class) {
        unset($this->filters->{$class::getName()});
    }

    public function compile(&$select = "*") {
        $transFilters = [];
        foreach ($this->filters as $filter) {
            $compiled = $filter->compile($select);
            if (!is_null($compiled)) {
                array_push($transFilters, $compiled);
            }
        }
        if (count($transFilters) > 0) {
            $filter = $this->_join('&', $transFilters);
            $filterName = $this::getName($this->id);
            $expr = "ANY($filter FOR t IN trans)";
            $select .= ", $expr as $filterName";

            $exclude = (int)!$this->exclude;
            return [[$filterName, $exclude]];
        } else {
            return [];
        }
    }
}
