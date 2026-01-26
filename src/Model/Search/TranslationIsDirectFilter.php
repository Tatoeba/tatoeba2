<?php

namespace App\Model\Search;

class TranslationIsDirectFilter extends BoolFilter {
    use TranslationFilterTrait;

    protected function getAttributeName() {
        return 'd';
    }

    public function compileToQueryExp($exp, $query) {
        return $exp->eq('is_direct', !$this->exclude);
    }
}
