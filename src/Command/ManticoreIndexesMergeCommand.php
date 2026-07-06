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

class ManticoreIndexesMergeCommand extends ManticoreIndexesCommandBase
{
    public static function defaultName(): string
    {
        return 'sphinx_indexes merge';
    }

    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();
        $parser
            ->setDescription('Merges the (big and slow to refresh) main index with the (small and quick to refresh) delta index of the given language ISO codes, or all languages needing merge if none provided.')
            ->addArgument('languages', [
                'help' => 'Limit operation to indexes of the provided space-separated list of three-letter ISO language codes.',
            ]);

        return $parser;
    }

    private function merge_index($lang, $io) {
        $io->out("Merging indexes of $lang... ", 0);
        system(
            "sudo -u {$this->sphinx_user} indexer --quiet --rotate --drop-src " .
            "--merge {$lang}_main_index {$lang}_delta_index",
            $return_value
        );
        if ($return_value != 0) {
            $io->error("failed.");
            return;
        }

        /* Remove sentences that were indexed */
        $conditions = array('lang' => $lang, 'indexed' => true);
        $this->fetchTable('ReindexFlags')->deleteAll($conditions, false);
        $io->out("ok");
    }

    protected function executeOperation(Arguments $args, ConsoleIo $io)
    {
        if ($args->getArguments()) {
            $langs = $this->validate_langs($args->getArguments(), $io);
        } else {
            $langs = $this->fetchTable('ReindexFlags')
                 ->find('all')
                 ->select('lang')
                 ->where(['indexed' => 1])
                 ->distinct('lang')
                 ->all()
                 ->extract('lang');
        }
        foreach ($langs as $lang) {
            $this->merge_index($lang, $io);
        }
    }
}
