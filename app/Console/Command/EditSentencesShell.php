<?php
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

App::import('Model', 'Sentence');
App::import('Model', 'User');

class EditSentencesShell extends Shell {

    public $uses = array('Sentence', 'User');

    private $stderr = null;

    private function be($username) {
        putenv('HTTP_CLIENT_IP=127.0.0.1');
        $editor = $this->User->findByUsername($username);
        if ($editor === false) {
            die("'$username' is not a valid username.\n");
        }
        CurrentUser::store($editor);
    }

    public function main() {
        $nb_sentences = $nb_ignored = 0;
        $stdin = fopen('php://stdin', 'r');

        if (count($this->args) != 1) {
            $myself = basename(__FILE__, '.php');
            die("Usage: $myself <editor> < sentences.csv\n\nMakes <editor> mass-edit sentences from CSV.\nCSV must be tab-separated, first column is\nthe sentence id, second column is the sentence text.\n");
        }

        $this->be($this->args[0]);
        while (($data = fgetcsv($stdin, 0, "\t")) !== FALSE) {
            if (count($data) != 2) {
                echo "Invalid line skipped.\n";
                continue;
            }

            list($id, $text) = $data;
            $sentence = $this->Sentence->findById($id);
            if ($sentence === false) {
                echo "Sentence $id does not exists, skipping!\n";
                $nb_ignored++;
                continue;
            }

            if ($text === $sentence['Sentence']['text']) {
                echo "Contents of sentence $id already set, skipping!\n";
                $nb_ignored++;
                continue;
            }

            $this->Sentence->save(array(
                'id' => $id,
                'text' => $text,
                'lang' => $sentence['Sentence']['lang'],
            ));
            echo ".";
            $nb_sentences++;
        }
        fclose($stdin);

        echo "\n$nb_sentences sentences edited, $nb_ignored ignored.\n";
    }
}
