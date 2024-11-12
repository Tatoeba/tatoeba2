<?php

namespace App\Model\Search;

use App\Model\Exception\InvalidValueException;
use App\Model\Search;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\ModelAwareTrait;
use Cake\Utility\Hash;

class IsNativeFilter extends SearchFilter {
    use ModelAwareTrait;

    private $sphinxFilterArrayLimit = 4096;
    private $lang;

    public function __construct($lang) {
        $this->lang = Search::validateLanguage($lang);
    }

    protected function getAttributeName() {
        return 'user_id';
    }

    private function calcFilter() {
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

        if (count($natives) <= $this->sphinxFilterArrayLimit) {
            $this->anyOf($natives);
        } else {
            $nonNatives = $this->UsersLanguages->find()
                ->where(function (QueryExpression $exp) {
                    $isNonNative = $exp->or(['level is' => null])->notEq('level', 5);
                    return $exp->add($isNonNative)
                               ->eq('language_code', $this->lang);
                })
                ->select(['of_user_id'])
                ->enableHydration(false)
                ->toList();
            $nonNatives = Hash::extract($nonNatives, '{n}.of_user_id');
            $filter = [];
            while (count($nonNatives)) {
                $excludedIds = array_splice($nonNatives, 0, $this->sphinxFilterArrayLimit);
                $this->not()->anyOf($excludedIds)->and();
            }
        }
    }

    public function compile() {
        $this->calcFilter();
        return parent::compile();
    }

    public function setSphinxFilterArrayLimit($limit) {
        $this->sphinxFilterArrayLimit = $limit;
    }
}
