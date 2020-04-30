<?php
namespace App\Model;

use App\Lib\LanguagesLib;

class Search {
    use \Cake\Datasource\ModelAwareTrait;

    private $query;
    private $lang;
    private $ownerId;
    private $hasOwner;

    private function asSphinxIndex($lang) {
        if ($lang) {
            return [$lang. '_main_index', $lang . '_delta_index'];
        } else {
            return ['und_index'];
        }
    }

    public function asSphinx() {
        $sphinx = [
            'index' => $this->asSphinxIndex($this->lang),
        ];
        if (!is_null($this->query)) {
            $sphinx['query'] = $this->query;
        }
        if ($this->ownerId) {
            $sphinx['filter'][] = ['user_id', $this->ownerId];
        }
        if (!is_null($this->hasOwner)) {
            $sphinx['filter'][] = ['user_id', 0, !$this->hasOwner];
        }
        return $sphinx;
    }

    public function filterByQuery($query) {
        $this->query = $query;
    }

    public function filterByLanguage($lang) {
        if (LanguagesLib::languageExists($lang)) {
            $this->lang = $lang;
        }
    }

    public function filterByOwnerName($owner) {
        if (!empty($owner)) {
            $this->loadModel('Users');
            $result = $this->Users->findByUsername($owner, ['fields' => ['id']])->first();
            if ($result) {
                $this->ownerId = $result->id;
            } else {
                return false;
            }
        }
        return true;
    }

    public function filterByOwnership($hasOwner) {
        if (in_array($hasOwner, ['yes', 'no'])) {
            $this->hasOwner = $hasOwner == 'yes';
        }
    }
}
