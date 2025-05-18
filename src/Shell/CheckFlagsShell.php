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

use App\Component\LanguageDetection;
use App\Model\Sentence;
use App\View\Helper\Languages;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\I18n\I18n;

class CheckFlagsShell extends Shell {

    public $uses = array('Sentence');

    private $tatoeba_languages = array();
    private $stdout;

    private function die_usage($message = '') {
        $myself = basename(__FILE__, '.php');
        die("$message\nSearch wrong flags on sentences using language autodetection.\n\n"
           ."Usage:   $myself check_flags <lang> <action>\n"
           ."Example: $myself check_flags epo print\n\n"
           ."Available values for <action>:\n"
           ."print: prints mismatched flags as CSV.\n"
           ."       Format is <id>,<lang>,<detected-lang>,<text>.\n");
    }

    private function cmd_print($detectedAs, $sent) {
        $data = array(
            $sent['Sentence']['id'],
            $sent['Sentence']['lang'],
            $detectedAs,
            $sent['Sentence']['text']
        );
        fputcsv($this->stdout, $data);
    }

    private function check_flag($lang, $command) {
        $sents = $this->Sentence->find('all', array(
            'conditions' => array('Sentence.lang' => $lang),
            'contain' => array('User' => array(
                'fields' => array('username'),
            )),
        ));
        foreach ($sents as $sent) {
            $text = $sent['Sentence']['text'];
            $currentFlag = $sent['Sentence']['lang'];
            $user = $sent['User']['username'];
            $detectedAs = LanguageDetectionComponent::detectLang($text, $user);
            if ($currentFlag != $detectedAs) {
                $this->{$command}($detectedAs, $sent);
            }
        }
    }

    public function main() {
        $lang = array_shift($this->args);
        if (!isset($this->tatoeba_languages[$lang])) {
            $valids = implode(' ', array_keys($this->tatoeba_languages));
            $this->die_usage("Presumably invalid ISO language code: $lang\n"
                            ."Valid ones are: $valids.\n");
        }

        $command = 'cmd_'.array_shift($this->args);
        if (!method_exists($this, $command))
            $this->die_usage();

        $this->check_flag($lang, $command);
    }

    public function startup() {
        // don't loose time generating transcriptions
        Configure::write('AutoTranscriptions.enabled', false);

        $this->stdout = fopen('php://output', 'w');

        I18n::setLocale('en');
        $languagesHelper = new LanguagesHelper();
        $this->tatoeba_languages = $languagesHelper->onlyLanguagesArray();
    }
}
