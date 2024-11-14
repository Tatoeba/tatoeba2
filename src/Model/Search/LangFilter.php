<?php

namespace App\Model\Search;

class LangFilter extends SearchFilter {
    protected function getAttributeName() {
        return 'lang';
    }

    public function anyOf(array $values) {
        array_map('\App\Model\Search::validateLanguage', $values);
        return parent::anyOf($values);
    }
}
