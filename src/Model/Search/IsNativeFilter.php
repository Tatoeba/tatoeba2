<?php

namespace App\Model\Search;

class IsNativeFilter extends BoolFilter {
    protected function getAttributeName() {
        return 'owner_is_native';
    }
}
