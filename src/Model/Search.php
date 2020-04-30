<?php
namespace App\Model;

use App\Lib\LanguagesLib;

class Search {
    use \Cake\Datasource\ModelAwareTrait;

    private $lang;
    private $ownerId;

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
        if ($this->ownerId) {
            $sphinx['filter'][] = ['user_id', $this->ownerId];
        }
        return $sphinx;
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
}
