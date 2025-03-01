<?php

namespace App\Model\Search;

class TranslationLangFilter extends SearchFilter {
    use TranslationFilterTrait;

    protected function getAttributeName() {
        return 'l';
    }

    public function anyOf(array $values) {
        $values = array_map('\App\Model\Search::validateLanguage', $values);
        return parent::anyOf($values);
    }
}
