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

    public $uses = array('Sentence', 'Transcription', 'Contribution');

    private function detectTranscriptionsFor($data) {
        $result = array();
        foreach ($data as $row) {
            $model = key($row);
            $lang = isset($row[$model]['lang']) ?
                    $row[$model]['lang'] :
                    $row[$model]['sentence_lang'];
            $text = $row[$model]['text'];
            $script = $this->Transcription->detectScript($lang, $text);

            $result[] = array(
                'id' => $row[$model]['id'],
                'script' => $script,
                'modified' => false,
            );
        }
        return $result;
    }

    private function autogen($lang) {
        $langs = $lang ?
                 array($lang) :
                 $this->Transcription->transcriptableLanguages();

        foreach ($langs as $lang) {
            echo "=== Proccessing sentences in language '$lang' ===\n";
            echo "Generating new transcriptions";
            $proceeded = $this->allSentencesOperation('_autogen', array(
                'lang' => $lang
            ));
            echo "\n$proceeded transcriptions generated.\n";
        }
    }

    private function _autogen($sentences) {
        $sentenceIds = Set::classicExtract($sentences, '{n}.Sentence.id');
        $this->Transcription->deleteAll(
            array(
                'Transcription.user_id' => null,
                'Transcription.sentence_id' => $sentenceIds,
            ),
            false
        );

        $generated = 0;
        foreach ($sentences as $sentence) {
           $generated += $this->Transcription->generateAndSaveAllTranscriptionsFor($sentence);
        }
        return $generated;
    }

    private function setContributionsScript($lang) {
        $langs = $lang ?
                 array($lang) :
                 $this->Transcription->langsInNeedOfScriptAutodetection();
        $proceeded = $this->batchOperation(
            'Contribution',
            '_setScript',
            array(
                'conditions' => array('sentence_lang' => $langs),
                'fields' => array('id', 'sentence_lang', 'script', 'text'),
            )
        );
        $langs = implode(', ', $langs);
        echo "\nScript set for $proceeded contributions in lang(s) $langs.\n";
    }

    private function setSentencesScript($lang) {
        $langs = $lang ?
                 array($lang) :
                 $this->Transcription->langsInNeedOfScriptAutodetection();
        $proceeded = $this->allSentencesOperation('_setScript', array(
            'lang' => $langs,
        ));
        $langs = implode(', ', $langs);
        echo "\nScript set for $proceeded sentences in lang(s) $lang.\n";
    }

    private function _setScript($rows, $model) {
        $proceeded = 0;
        $data = $this->detectTranscriptionsFor($rows);
        $options = array(
            'validate' => true,
            'atomic' => false,
            'callbacks' => false,
        );
        if ($data && $this->{$model}->saveAll($data, $options))
            $proceeded += count($data);
        return $proceeded;
    }

    private function allSentencesOperation($operation, $conditions) {
        return $this->batchOperation('Sentence', $operation, array(
            'conditions' => $conditions,
            'fields' => array('id', 'lang', 'script', 'text'),
        ));
    }

    private function batchOperation($model, $operation, $options) {
        $batchSize = 1000;
        $proceeded = 0;
        $options = array_merge(
            array(
                'contain' => array(),
                'limit' => $batchSize,
                'offset' => 0,
            ),
            $options
        );

        do {
            $data = $this->{$model}->find('all', $options);
            $proceeded += $this->{$operation}($data, $model);
            echo ".";
            $options['offset'] += $batchSize;
        } while ($data);
        return $proceeded;
    }

    private function die_usage() {
        $me = basename(__FILE__, '.php');
        die(
            "\nWrites transcription-related information to the database.\n\n".
            "  Usage: $me script {sentences|contrubution} [lang]\n".
            "         $me autogen [lang]\n".
            "Example: $me script sentences\n\n".
            "Parameters:\n".
            " script: fills 'script' column in the sentences or contributions table.\n".
            "autogen: removes autogenerated transcriptions and regenerate them,\n".
            "         for all the sentences.\n"
        );
    }

    public function main() {
        if (count($this->args) < 1) {
            $this->die_usage();
        }
        $operation = array_shift($this->args);
        switch($operation) {
            case 'script':
                $table = array_shift($this->args);
                switch ($table) {
                    case 'sentences':
                        $this->setSentencesScript(array_shift($this->args));
                        break;
                    case 'contributions':
                        $this->setContributionsScript(array_shift($this->args));
                        break;
                    default:
                        $this->die_usage();
                }
                break;
            case 'autogen':
                $this->autogen(array_shift($this->args));
                break;
            default:
                $this->die_usage();
        }
    }
}
