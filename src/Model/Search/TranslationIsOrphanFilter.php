<?php

namespace App\Model\Search;

class TranslationIsOrphanFilter extends BoolFilter {
    use TranslationFilterTrait;

    protected $valueForTrue = 0;

    protected function getAttributeName() {
        return 'u';
    }
}
