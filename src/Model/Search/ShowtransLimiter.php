<?php

namespace App\Model\Search;

use App\Model\Search\TranslationFilterGroup;
use Cake\ORM\Query;

class ShowtransLimiter {
    private $filters = [];

    public function __construct(array $filters) {
        foreach ($filters as $filter) {
            if ($filter instanceof TranslationFilterGroup && !$filter->getExclude()) {
                $this->filters[] = $filter;
            }
        }
    }

    public function getFilters() {
        return $this->filters;
    }

    private function compileToOredQueryExp(Query $query) {
        $exp = $query->newExpr();
        $exps = [];
        foreach ($this->filters as $filter) {
            $exps[] = $filter->compileToQueryExp($query->newExpr(), $query);
        }
        $exps = array_filter($exps, fn($e) => $e->count());
        if ($exps) {
            if (count($exps) > 1) {
                $exp = $exp->or($exps);
            } else {
                $exp = $exps[0];
            }
        }
        return $exp;
    }

    public function limitTranslations($query) {
        $exp = $this->compileToOredQueryExp($query);
        if ($exp->count()) {
            $query->where($exp);
        }
    }
}
