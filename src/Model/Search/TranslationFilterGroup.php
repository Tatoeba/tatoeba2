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
        $exprs = [];
        foreach ($this->filters as $filter) {
            $compiled = $filter->compile($select);
            if (!is_null($compiled)) {
                if (is_array($compiled)) {
                    array_push($exprs, ...$compiled);
                } else {
                    array_push($transFilters, $compiled);
                }
            }
        }
        if (count($transFilters) > 0) {
            $filter = $this->_join('&', $transFilters);
            $exprs[] = "ANY($filter FOR t IN trans)";
        }
        if (count($exprs) > 0) {
            $expr = $this->_join('and', $exprs);
            $filterName = $this::getName($this->id);
            $select .= ", $expr as $filterName";

            $exclude = (int)!$this->exclude;
            return [[$filterName, $exclude]];
        } else {
            return [];
        }
    }
}
