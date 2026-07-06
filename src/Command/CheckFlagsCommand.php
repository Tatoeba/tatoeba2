<?php
declare(strict_types=1);

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
namespace App\Command;

use App\Controller\Component\LanguageDetectionComponent;
use App\Lib\LanguagesLib;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\I18n\I18n;

class CheckFlagsCommand extends Command
{
    private $tatoeba_languages = array();
    private $stdout;

    private function cmd_print($detectedAs, $sent) {
        $data = array(
            $sent->id,
            $sent->lang,
            $detectedAs,
            $sent->text,
        );
        fputcsv($this->stdout, $data);
    }

    private function check_flag($lang, $command) {
        $sents = $this->fetchTable('Sentences')->find('all', array(
            'conditions' => array('lang' => $lang),
            'contain' => array('Users' => array(
                'fields' => array('username'),
            )),
        ));
        $detector = new LanguageDetectionComponent(new \Cake\Controller\ComponentRegistry(new \Cake\Controller\Controller(new \Cake\Http\ServerRequest())));
        foreach ($sents as $sent) {
            $text = $sent->text;
            $currentFlag = $sent->lang;
            $user = $sent->user ? $sent->user->username : '';
            $detectedAs = $detector->detectLang($text, $user);
            if ($currentFlag != $detectedAs) {
                $this->{$command}($detectedAs, $sent);
            }
        }
    }

    public function initialize(): void {
        // don't loose time generating transcriptions
        Configure::write('AutoTranscriptions.enabled', false);

        $this->stdout = fopen('php://output', 'w');

        I18n::setLocale('en');
        $this->tatoeba_languages = LanguagesLib::languagesInTatoeba();
    }

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        $parser
            ->setDescription('Search wrong flags on sentences using language autodetection.')
            ->addArgument('language', [
                'help' => 'three-letter ISO code of the language of sentences to perform autodetection into',
                'required' => true,
            ])
            ->setEpilog([
                'This command prints out mismatched flags as CSV.',
                'Format is <id>,<lang>,<detected-lang>,<text>.',
            ]);

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $lang = $args->getArgument('language');
        if (!isset($this->tatoeba_languages[$lang])) {
            $valids = implode(' ', array_keys($this->tatoeba_languages));
            $io->err("Presumably invalid ISO language code: $lang\n"
                    ."Valid ones are: $valids.\n");
            return static::CODE_ERROR;
        }
        $this->check_flag($lang, 'cmd_print');
    }
}
