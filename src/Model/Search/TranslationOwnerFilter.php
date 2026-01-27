<?php

namespace App\Model\Search;

class TranslationOwnerFilter extends OwnerFilter {
    use TranslationFilterTrait;

    protected function getAttributeName() {
        return 'u';
    }

    public function compileToQueryExp($exp, $query) {
        foreach ($this->getMappedFilters() as list($exclude, $values)) {
            if ($exclude) {
                $exp->notIn('user_id', $values);
            } else {
                $exp->in('user_id', $values);
            }
        }
        return $exp;
    }
}
