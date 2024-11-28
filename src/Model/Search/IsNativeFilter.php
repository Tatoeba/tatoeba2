<?php

namespace App\Model\Search;

use App\Model\Exception\InvalidValueException;
use App\Model\Search;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\ModelAwareTrait;
use Cake\Utility\Hash;

class IsNativeFilter extends BoolFilter {
    use ModelAwareTrait;

    private $sphinxFilterArrayLimit = 4096;
    private $lang;

    protected function getAttributeName() {
        return 'user_id';
    }

    public function setLang(string $lang) {
        $this->lang = Search::validateLanguage($lang);
        return $this;
    }

    protected function calcFilter() {
        if (is_null($this->lang)) {
            throw new \RuntimeException("Precondition failed: setLang() was not called first");
        }
        $this->loadModel('UsersLanguages');
        $natives = $this->UsersLanguages->find()
            ->where([
                'language_code' => $this->lang,
                'level' => 5,
            ])
            ->select(['of_user_id'])
            ->enableHydration(false)
            ->toList();
        $natives = Hash::extract($natives, '{n}.of_user_id');

        if (count($natives) == 0) {
            // There are no native speakers in this language, so no sentences should
            // match at all. But if we set the filter with an empty array, it won't do
            // any filtering at all, so we use the dummy value of -1 to force a non-match.
            $this->anyOf([-1]);
        } elseif (count($natives) <= $this->sphinxFilterArrayLimit) {
            $this->anyOf($natives);
        } else {
            if (!$this->exclude) {
                $natives = $this->UsersLanguages->find()
                    ->where(function (QueryExpression $exp) {
                        $isNonNative = $exp->or(['level is' => null])->notEq('level', 5);
                        return $exp->add($isNonNative)
                                   ->eq('language_code', $this->lang);
                    })
                    ->select(['of_user_id'])
                    ->enableHydration(false)
                    ->toList();
                $natives = Hash::extract($natives, '{n}.of_user_id');
            }
            $filter = [];
            while (count($natives)) {
                $excludedIds = array_splice($natives, 0, $this->sphinxFilterArrayLimit);
                $this->not()->anyOf($excludedIds)->_and();
            }
        }
    }

    public function setSphinxFilterArrayLimit($limit) {
        $this->sphinxFilterArrayLimit = $limit;
    }
}
