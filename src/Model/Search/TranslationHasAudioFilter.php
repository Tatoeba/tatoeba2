<?php

namespace App\Model\Search;

class TranslationHasAudioFilter extends BoolFilter {
    use TranslationFilterTrait;

    protected function getAttributeName() {
        return 'a';
    }
}
