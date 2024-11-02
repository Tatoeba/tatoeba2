<?php

namespace App\Model\Search;

class TranslationIsUnapprovedFilter extends BoolFilter {
    use TranslationFilterTrait;

    protected $valueForTrue = 0;

    protected function getAttributeName() {
        return 'c';
    }
}
