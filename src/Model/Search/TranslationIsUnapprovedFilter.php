<?php

namespace App\Model\Search;

class TranslationIsUnapprovedFilter extends BoolFilter {
    use TranslationFilterTrait;

    protected $valueForTrue = 0;

    protected $valueForTrueInDatabase = -1;

    protected function getAttributeName() {
        return 'c';
    }

    public function compileToQueryExp($exp, $query) {
        if ($this->exclude) {
            $exp->notEq('correctness', $this->valueForTrueInDatabase);
        } else {
            $exp->eq('correctness', $this->valueForTrueInDatabase);
        }
        return $exp;
    }
}
