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
App::import('Model', 'Transcription');

class TranscriptionsShell extends Shell {

    public $uses = array('Sentence', 'Transcription');

    private function detectTranscriptionsFor($sentences) {
        $result = array();
        foreach ($sentences as $sentence) {
            $script = $this->Transcription->detectScript($sentence['Sentence']['lang'], $sentence['Sentence']['text']);
            $result[] = array(
                'id' => $sentence['Sentence']['id'],
                'script' => $script,
            );
        }
        return $result;
    }

    private function setScript() {
        $conditions = array(
            'lang' => $this->Transcription->langsInNeedOfScriptAutodetection(),
        );
        $batchSize = 1000;
        $offset = 0;
        $proceeded = 0;

        do {
            $sentences = $this->Sentence->find('all', array(
                'conditions' => $conditions,
                'fields' => array('id', 'lang', 'text'),
                'contain' => array(),
                'limit' => $batchSize,
                'offset' => $offset,
            ));
            $data = $this->detectTranscriptionsFor($sentences);
            $options = array(
                'validate' => true,
                'atomic' => false,
            );
            if ($this->Sentence->saveAll($data, $options))
                $proceeded += count($data);
            echo ".";
            $offset += $batchSize;
        } while ($sentences);
        echo "\nScript set for $proceeded sentences.\n";
    }

    private function die_usage() {
        $me = basename(__FILE__, '.php');
        die("\nFills the database with information required for ".
            "transcriptions.\n\n".
            "  Usage: $me <what>\n".
            "Example: $me script\n\n".
            "Parameters:\n".
            "<what>: type of information.\n");
    }

    public function main() {
        if (count($this->args) < 1) {
            $this->die_usage();
        }
        $operation = $this->args[0];
        switch($operation) {
            case 'script':
                $this->setScript();
                break;
            default:
                $this->die_usage();
        }
    }
}
