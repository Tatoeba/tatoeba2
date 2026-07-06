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

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;

class ManticoreIndexesUpdateCommand extends ManticoreIndexesCommandBase
{
    public static function defaultName(): string
    {
        return 'sphinx_indexes update';
    }

    private function reload_manticore_config($io)
    {
        $port = Configure::read('Sphinx.sphinxql_port');
        try {
            $pdo = new \PDO("mysql:host=127.0.0.1;port=$port");
            $ok = $pdo->exec("RELOAD INDEXES");
            if ($ok === FALSE) {
                $error = $pdo->errorInfo();
            }
        } catch (\PDOException $e) {
            $error = $e->getMessage();
        }
        if (isset($error)) {
            $io->warning("Warning: unable to tell Manticore to reload its config: $error");
            $io->warning("You may have to restart Manticore if there is any new language.");
        }
    }

    private function update_index($io, $type, $langs, $force)
    {
        if (!$langs) {
            if ($type == 'delta' && !$force) {
                $langs = $this->fetchTable('ReindexFlags')
                     ->find('list', valueField: 'lang')
                     ->select(['lang'])
                     ->where(['indexed' => 0])
                     ->groupBy('lang')
                     ->all()
                     ->toArray();
            } else {
                $Sentences = $this->fetchTable('Sentences');
                $langs = $Sentences->languagesHavingSentences();
            }
        }
        $langs = array_filter($langs, function($lang) {
            return isset($this->tatoeba_languages[$lang]);
        });

        if (empty($langs)) {
            $io->info("None of the $type indexes need updating");
        } else {
            $io->out("Updating $type indexes... ", 0);
            $indexes = implode(' ', array_map(
                function($lang) use ($type) { return "{$lang}_{$type}_index"; },
                $langs
            ));
            system(
                "sudo -u {$this->sphinx_user} indexer --quiet --sighup-each --rotate $indexes",
                $return_value
            );
            if ($return_value == 0) {
                $io->out("OK.");
                $this->reload_manticore_config($io);
            } else {
                $io->error("Failed.");
            }
        }
    }

    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();
        $parser
            ->setDescription('Refresh Manticore indexes.')
            ->addArgument('type', [
                'help' => 'index type',
                'choices' => ['main', 'delta'],
                'required' => true,
            ])
            ->addArgument('languages', [
                'help' => 'Limit operation to indexes of the provided space-separated list of three-letter ISO language codes.',
            ])
            ->addOption('force', [
                'short' => 'f',
                'boolean' => true,
                'help' => "Force updating all indexes (useful for delta)",
            ]);

        return $parser;
    }

    protected function executeOperation(Arguments $args, ConsoleIo $io)
    {
        $type = $args->getArgument('type');

        $langs = $args->getArguments();
        array_shift($langs);
        if ($langs) {
            $langs = $this->validate_langs($langs, $io);
        } else {
            $langs = null;
        }

        $this->update_index($io, $type, $langs, $args->getOption('force'));
    }
}
