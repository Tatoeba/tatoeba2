<?php

namespace App\Model\Search;

class TranslationIsNativeFilter extends BoolFilter {
    use TranslationFilterTrait;

    protected function getAttributeName() {
        return 'n';
    }
}
