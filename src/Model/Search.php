<?php
namespace App\Model;

use App\Lib\LanguagesLib;

class Search {
    use \Cake\Datasource\ModelAwareTrait;

    private $lang;

    private function asSphinxIndex($lang) {
        if ($lang) {
            return [$lang. '_main_index', $lang . '_delta_index'];
        } else {
            return ['und_index'];
        }
    }

    public function asSphinx() {
        return [
            'index' => $this->asSphinxIndex($this->lang),
        ];
    }

    public function filterByLanguage($lang) {
        if (LanguagesLib::languageExists($lang)) {
            $this->lang = $lang;
        }
    }
}
