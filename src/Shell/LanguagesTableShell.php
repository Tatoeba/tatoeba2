<?php
/**
 *  Tatoeba Project, free collaborative creation of languages corpuses project
 *  Copyright (C) 2018  Gilles Bedel
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
namespace App\Shell;

use App\Lib\LanguagesLib;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;


class InitLanguagesTableShell extends Shell {

    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Languages');
    }

    private function get_tatoeba_languages() {
        Configure::write('Config.language', 'eng');
        return LanguagesLib::languagesInTatoeba();
    }

    private function die_usage($message = '') {
        $myself = basename(__FILE__, '.php');
        die("$message\nInitialize the `languages` table with values.\n\n"
           ."Usage: $myself\n");
    }

    private function removeStats() {
        $this->Languages->deleteAll('1=1');
    }

    private function insertLanguages() {
        $codes = array_keys($this->get_tatoeba_languages());
        $data = array_map(
            function ($code) {
                return array('code' => $code);
            },
            $codes
        );
        $data[] = [ 'code' => null ];
        $entities = $this->Languages->newEntities($data);
        $this->Languages->saveMany($entities);
    }

    private function insertStats() {
        $updateScript = ROOT . '/docs/database/scripts/update_languages_stats.sql';
        $script = file_get_contents($updateScript);
        $conn = ConnectionManager::get('default');
        $conn->execute($script);
    }

    private function run() {
        if (count($this->args)) {
            die_usage("Error: no parameters required.");
        }
        $this->removeStats();
        $this->insertLanguages();
        $this->insertStats();
    }

    public function main() {
        return $this->run();
    }
}
