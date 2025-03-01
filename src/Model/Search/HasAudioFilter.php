<?php

namespace App\Model\Search;

class HasAudioFilter extends BoolFilter {
    protected function getAttributeName() {
        return 'has_audio';
    }
}
