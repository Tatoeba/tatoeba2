<?php

namespace App\Model\Search;

class TranslationIsDirectFilter extends BoolFilter {
    use TranslationFilterTrait;

    protected function getAttributeName() {
        return 'd';
    }
}
