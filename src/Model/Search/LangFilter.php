<?php

namespace App\Model\Search;

class LangFilter extends SearchFilter {
    protected function getAttributeName() {
        return 'lang';
    }

    public function not() {
        throw new \App\Model\Exception\InvalidNotOperatorException($this);
    }

    public function and() {
        throw new \App\Model\Exception\InvalidAndOperatorException($this);
    }

    public function anyOf(array $values) {
        array_map(fn($l) => \App\Model\Search::validateLanguage($l, $this), $values);
        return parent::anyOf($values);
    }
}
