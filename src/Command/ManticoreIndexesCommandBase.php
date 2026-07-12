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

use App\Console\VarArgsConsoleOptionParser;
use App\Lib\LanguagesLib;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

define('LOCK_FILE', sys_get_temp_dir() . DS . basename(__FILE__) . '.lock');

abstract class ManticoreIndexesCommandBase extends Command
{
    public $sphinx_user = 'manticore';

    protected $tatoeba_languages;

    public function initialize(): void {
        $this->tatoeba_languages = LanguagesLib::languagesInTatoeba();
    }

    public function getOptionParser(): ConsoleOptionParser
    {
        [$root, $base] = explode(' ', $this->name);
        $myself = "$root $base";
        $parser = new VarArgsConsoleOptionParser($this->defaultName());
        $parser
            ->addOption('wait', [
                'short' => 'w',
                'boolean' => true,
                'help' => "Wait for any running '$myself' to exit",
            ]);

        return $parser;
    }

    protected function validate_langs($langs, $io) {
        foreach ($langs as $lang) {
            if (!isset($this->tatoeba_languages[$lang])) {
                $valids = implode(' ', array_keys($this->tatoeba_languages));
                $io->error([
                    "Presumably invalid ISO language code: $lang",
                    "Valid ones are: $valids.",
                ]);
                $this->abort();
            }
        }
        return $langs;
    }

    private function waitFor($pid) {
        while ($this->isProcessRunning($pid)) {
            sleep(1);
        }
    }

    private function isProcessRunning($pid) {
        return (bool)posix_getpgid($pid);
    }

    abstract protected function executeOperation(Arguments $args, ConsoleIo $io);

    public function execute(Arguments $args, ConsoleIo $io)
    {
        if (!posix_getpwnam($this->sphinx_user)) {
            $io->error("No such user: {$this->sphinx_user}");
            return static::CODE_ERROR;
        }

        exec("sudo -u {$this->sphinx_user} indexer -h", $output, $return);
        if ($return !== 0) {
            $io->error("You need to be able to run 'indexer' as user '{$this->sphinx_user}'.");
            return static::CODE_ERROR;
        }

        if (file_exists(LOCK_FILE)) {
            $pid = (int)file_get_contents(LOCK_FILE);
            if ($this->isProcessRunning($pid)) {
                if ($args->getOption('wait')) {
                    $io->info("Waiting for process $pid to terminate...");
                    $this->waitFor($pid);
                } else {
                    $io->info("Exiting because another instance of this script "
                             ."seems to be running. If you're sure it's not, "
                             ."remove the file '".LOCK_FILE."'.");
                    $this->abort();
                }
            }
        }

        $fh = fopen(LOCK_FILE, 'w');
        if ($fh) {
            fwrite($fh, (string)getmypid());
            fclose($fh);
        } else {
            $io->error("Cannot write lock file '".LOCK_FILE."'.\n");
            return static::CODE_ERROR;
        }

        try {
            return $this->executeOperation($args, $io);
        } finally {
            @unlink(LOCK_FILE);
        }
    }
}
