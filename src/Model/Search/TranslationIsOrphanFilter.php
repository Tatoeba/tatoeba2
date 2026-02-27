<?php

namespace App\Model\Search;

class TranslationIsOrphanFilter extends BoolFilter {
    use TranslationFilterTrait;

    protected $valueForTrue = 0;

    protected function getAttributeName() {
        return 'u';
    }

    public function compileToQueryExp($exp, $query) {
        if ($this->exclude) {
            $exp->isNotNull('user_id');
        } else {
            $exp->isNull('user_id');
        }
        return $exp;
    }
}
