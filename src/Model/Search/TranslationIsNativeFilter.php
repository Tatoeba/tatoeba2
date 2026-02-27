<?php

namespace App\Model\Search;

class TranslationIsNativeFilter extends BoolFilter {
    use TranslationFilterTrait;

    protected function getAttributeName() {
        return 'n';
    }

    public function compileToQueryExp($exp, $query) {
        $query->leftJoinWith('OwnerLanguage', function ($q) {
            return $q->where(['OwnerLanguage.level' => 5]);
        });
        if ($this->exclude) {
            $exp->isNull('OwnerLanguage.id');
        } else {
            $exp->isNotNull('OwnerLanguage.id');
        }
        return $exp;
    }
}
