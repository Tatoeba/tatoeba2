<?php
/**
 *  Tatoeba Project, free collaborative creation of languages corpuses project
 *  Copyright (C) 2014  Gilles Bedel
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

App::import('Helper');
App::import('Helper', 'Languages');
App::import('Model', 'Sentence');

class SphinxIndexesShell extends Shell {

    public $searchd_user = 'sphinxsearch';

    private $tatoeba_languages;

    private function get_tatoeba_languages() {
        Configure::write('Config.language', 'eng');
        $languagesHelper = new LanguagesHelper();
        $this->tatoeba_languages = $languagesHelper->onlyLanguagesArray();
    }

    private function die_usage($message = '') {
        $myself = basename(__FILE__, '.php');
        die("$message\nThis manages the sphinx indexes.\n\n"
           ."Usage:   $myself merge [lang]...\n"
           ."Example: $myself merge eng fra epo\n"
           ."Merges the (big and slow to refresh) main index with "
           ."the (small and quick to refresh) delta index of the given "
           ."language ISO codes, or all the languages if none provided.\n\n"
           ."Usage:   $myself update ( main | delta )\n"
           ."Example: $myself update delta\n"
           ."Updates all the indexes of the given type.\n");
    }

    private function merge_index($lang) {
        echo "Merging indexes of $lang... ";
        system("indexer --quiet --rotate "
              ."--merge ${lang}_main_index ${lang}_delta_index",
               $return_value);
        if ($return_value != 0) {
            echo "failed.\n";
            return;
        }
        # Update the delta discriminant so that future indexing of
        # the delta index will properly complete the newly merged index
        $Sentence = ClassRegistry::init('Sentence');
        $Sentence->recursive = -1; 
        $most_recent = $Sentence->find('first', array(
            'sphinx' => array(
                'index' => array("${lang}_delta_index"),
                'sortMode' => array(SPH_SORT_ATTR_DESC => 'modified'),
            ),
            'search' => '',
        ));
        $max_indexed_date = $most_recent["Sentence"]["modified"];
        if (!$max_indexed_date) {
            echo "no new sentences found\n";
            return;
        }
        $Sentence->query("
            REPLACE INTO sphinx_delta
                SELECT languages.id, '$max_indexed_date'
                FROM languages, sentences
                WHERE languages.code = '$lang'
                AND sentences.lang = languages.code");
        echo "ok\n";
    }

    private function update_index($type) {
        echo "Updating all the $type indexes...\n";
        $indexes = implode(' ', array_map(
            function($lang) use ($type) { return "${lang}_${type}_index"; },
            array_keys($this->tatoeba_languages)
        ));
        system("indexer --rotate $indexes", $return_value);
        echo ($return_value == 0) ? "OK.\n" : "Failed.\n";
    }

    private function check_prerequistes() {
        $processUser = posix_getpwuid(posix_geteuid());
        if ($this->searchd_user != $processUser['name']) {
            die("You must run this script as user '{$this->searchd_user}'.\n");
        }
    }

    private function process_args() {
        $command = array_shift($this->args);
        switch($command) {
            case 'merge':
                $langs = count($this->args) ?
                         $this->args : array_keys($this->tatoeba_languages);
                foreach ($langs as $lang) {
                    if (!isset($this->tatoeba_languages[$lang])) {
                        $valids = implode(' ', array_keys($this->tatoeba_languages));
                        $this->die_usage("Presumably invalid ISO language code: $lang\n"
                                        ."Valid ones are: $valids.\n");
                    }
                }
                foreach ($langs as $lang) {
                    $this->merge_index($lang);
                }
                break;

            case 'update':
                $type = $this->args[0];
                if ($type != 'main' && $type != 'delta') {
                    $this->die_usage("Invalid index type: $type\n"
                                    ."Must be either main of index.\n");
                }
                $this->update_index($type);
                break;

            default:
                $this->die_usage("Invalid command: $command\n");
        }
    }

    public function main() {
        $this->check_prerequistes();
        $this->get_tatoeba_languages();
        $this->process_args();
    }
}
