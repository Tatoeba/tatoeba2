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
namespace App\Shell;

use App\Lib\LanguagesLib;
use App\Model\ReindexFlag;
use Cake\Console\Shell;
use Cake\Core\Configure;


define('LOCK_FILE', sys_get_temp_dir() . DS . basename(__FILE__) . '.lock');

class SphinxIndexesShell extends Shell {

    public $sphinx_user = 'manticore';

    private $tatoeba_languages;

    private function get_tatoeba_languages() {
        Configure::write('Config.language', 'eng');
        $this->tatoeba_languages = LanguagesLib::languagesInTatoeba();
    }

    private function die_usage($message = '') {
        $myself = basename(__FILE__, '.php');
        $this->_die("$message\nThis manages the sphinx indexes.\n\n"
           ."Usage:   $myself [-w] merge [lang]...\n"
           ."Example: $myself merge eng fra epo\n"
           ."Merges the (big and slow to refresh) main index with "
           ."the (small and quick to refresh) delta index of the given "
           ."language ISO codes, or all the languages if none provided.\n\n"
           ."Usage:   $myself [-w] update ( main | delta )\n"
           ."Example: $myself update delta\n"
           ."Updates all the indexes of the given type.\n\n"
           ."-w:      wait for running $myself to exit\n");
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

        /* Remove sentences that were indexed */
        $this->loadModel('ReindexFlags');
        $conditions = array('lang' => $lang, 'indexed' => true);
        $this->ReindexFlags->deleteAll($conditions, false);
        echo "ok\n";
    }

    private function update_index($type) {
        if ($type == 'delta') {
            $this->loadModel('ReindexFlags');
            $langs = $this->ReindexFlags
                 ->find('list', ['valueField' => 'lang'])
                 ->select(['lang'])
                 ->where(['lang is not' => null])
                 ->group('lang')
                 ->all()
                 ->toArray();
        } else {
            $langs = array_keys($this->tatoeba_languages);
        }

        if (empty($langs)) {
            echo "None of the $type indexes need updating\n";
        } else {
            echo "Updating $type indexes...\n";
            $indexes = implode(' ', array_map(
                function($lang) use ($type) { return "${lang}_${type}_index"; },
                $langs
            ));
            system("indexer --rotate $indexes", $return_value);
            echo ($return_value == 0) ? "OK.\n" : "Failed.\n";
        }
    }

    private function become_sphinx_user() {
        $sphinxUserInfo = posix_getpwnam($this->sphinx_user);
        if (!$sphinxUserInfo) {
            $this->_die("No such user: {$this->sphinx_user}\n");
        }
        if (!posix_setuid($sphinxUserInfo['uid'])) {
            $this->_die("Unable to change uid. Make sure you're root.\n");
        }
    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addOption('wait', [
            'short' => 'w',
            'boolean' => true,
        ]);
        return $parser;
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

    private function _die($message = null) {
        @unlink(LOCK_FILE);
        die($message);
    }

    private function run() {
        $this->get_tatoeba_languages();
        $this->process_args();
    }

    private function waitFor($pid) {
        echo "Waiting for process $pid to terminate...\n";
        while ($this->isProcessRunning($pid)) {
            sleep(1);
        }
    }

    private function isProcessRunning($pid) {
        return (bool)posix_getpgid($pid);
    }

    public function main() {
        $this->become_sphinx_user();

        if (file_exists(LOCK_FILE)) {
            $pid = file_get_contents(LOCK_FILE);
            if ($this->isProcessRunning($pid)) {
                if ($this->param('wait')) {
                    $this->waitFor($pid);
                } else {
                    die("Exiting because another instance of this script "
                       ."seems to be running. If you're sure it's not, "
                       ."remove the file '".LOCK_FILE."'.\n");
                }
            }
        }

        $fh = fopen(LOCK_FILE, 'w');
        if ($fh) {
            fwrite($fh, getmypid());
            fclose($fh);
        } else {
            die("Cannot write lock file '".LOCK_FILE."'.\n");
        }
        $this->run();
        $this->_die();
    }
}
