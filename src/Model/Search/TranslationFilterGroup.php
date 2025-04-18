<?php

namespace App\Model\Search;

class TranslationFilterGroup extends BaseSearchFilter {
    use FiltersCollectionTrait;

    private $alias;

    public function getAlias() {
        return $this->alias;
    }

    public static function getName($id = '') {
        return "tf{$id}";
    }

    public function __construct(string $id = '') {
        $this->alias = self::getName($id);
    }

    public function setExclude(bool $exclude = true) {
        $this->exclude = $exclude;
        return $this;
    }

    protected function _compile() {}

    public function compile() {
        $transFilters = [];
        $exprs = [];
        foreach ($this->filters as $filter) {
            $compiled = $filter->compile();
            if (!is_null($compiled)) {
                if (is_array($compiled)) {
                    array_push($exprs, ...$compiled);
                } else {
                    array_push($transFilters, $compiled);
                }
            }
        }
        $exprs = array_map(
            function ($f) {
                // this is just to wrap inside NOT() if $f[2] is true
                return $this->_join('', [ $f[1] ], $f[2] ?? false);
            },
            $exprs
        );
        if (count($transFilters) > 0) {
            $filter = $this->_join('&', $transFilters);
            $exprs[] = "ANY($filter FOR t IN trans)";
        }
        if (count($exprs) > 0) {
            $expr = $this->_join('and', $exprs);
            $filterName = $this->getAlias();
            return [[$filterName, $expr, $this->exclude]];
        } else {
            return [];
        }
    }
}
