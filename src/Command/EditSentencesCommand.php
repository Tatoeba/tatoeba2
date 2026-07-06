<?php
declare(strict_types=1);

/**
 *  Tatoeba Project, free collaborative creation of languages corpuses project
 *  Copyright (C) 2015  Gilles Bedel
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

use App\Model\CurrentUser;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class EditSentencesCommand extends Command
{
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        $parser
            ->setDescription('Mass-edit text of sentences from CSV.')
            ->addArgument('editor', [
                'help' => 'Name of the user to perform edits as.',
                'required' => true,
            ])
            ->addArgument('file', [
                'help' => 'CSV file to read edition instructions from. Must be tab-separated, first column is the sentence id, second column is the sentence text.',
                'required' => true,
            ]);

        return $parser;
    }

    private function be(string $username): bool {
        $editor = $this->fetchTable('Users')->findByUsername($username)->first();
        if (is_null($editor)) {
            return false;
        }
        CurrentUser::store($editor->toArray());
        return true;
    }

    public function execute(Arguments $args, ConsoleIo $io) {
        $nb_sentences = $nb_ignored = 0;

        $file = $args->getArgument('file');
        $csv = fopen($args->getArgument('file'), 'r');
        if ($csv === false) {
            $io->error("Unable to open file '$file'.");
            return static::CODE_ERROR;
        }

        $editor = $args->getArgument('editor');
        if (!$this->be($editor)) {
            $io->error("'$editor' is not a valid username.");
            return static::CODE_ERROR;
        }

        $Sentences = $this->fetchTable('Sentences');
        while (($data = fgetcsv($csv, 0, "\t")) !== FALSE) {
            if (count($data) != 2) {
                $io->warning("Invalid line skipped.");
                continue;
            }

            list($id, $text) = $data;
            $sentence = $Sentences->findById($id)->first();
            if (is_null($sentence)) {
                $io->warning("Sentence $id does not exist, skipping!");
                $nb_ignored++;
                continue;
            }

            if ($text === $sentence->text) {
                $io->warning("Contents of sentence $id already set, skipping!");
                $nb_ignored++;
                continue;
            }

            $Sentences->editSentence([
                'id' => $id,
                'text' => $text,
            ]);
            $io->out(".", 0);
            $nb_sentences++;
        }
        fclose($csv);

        $io->info("\n$nb_sentences sentences edited, $nb_ignored ignored.");
        return static::CODE_SUCCESS;
    }
}
