<?php

namespace App\Model\Search;

class TranslationOwnerFilter extends OwnerFilter {
    use TranslationFilterTrait;

    protected function getAttributeName() {
        return 'u';
    }
}
