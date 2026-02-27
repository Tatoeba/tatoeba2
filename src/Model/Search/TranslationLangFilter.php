<?php

namespace App\Model\Search;

class TranslationLangFilter extends SearchFilter {
    use TranslationFilterTrait;

    protected function getAttributeName() {
        return 'l';
    }

    public function anyOf(array $values) {
        array_map(fn($l) => \App\Model\Search::validateLanguage($l, $this), $values);
        return parent::anyOf($values);
    }

    public function compileToQueryExp($exp, $query) {
        foreach ($this->getMappedFilters() as list($exclude, $values)) {
            if ($exclude) {
                $exp->notIn('lang', $values);
            } else {
                $exp->in('lang', $values);
            }
        }
        return $exp;
    }
}
