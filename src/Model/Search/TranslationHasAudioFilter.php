<?php

namespace App\Model\Search;

class TranslationHasAudioFilter extends BoolFilter {
    use TranslationFilterTrait;

    protected function getAttributeName() {
        return 'a';
    }

    public function compileToQueryExp($exp, $query) {
        $query->leftJoinWith('Audios');
        if ($this->exclude) {
            $exp->isNull('Audios.id');
        } else {
            $exp->isNotNull('Audios.id');
            $query->distinct();
        }
        return $exp;
    }
}
