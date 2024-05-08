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
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Utility\Hash;
use App\Model\Sentence;
use App\Model\Transcription;


class TranscriptionsShell extends Shell {

    use BatchOperationTrait;

    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Transcriptions');
    }

    private function detectTranscriptionsFor($entities) {
        foreach ($entities as $entity) {
            $lang = isset($entity->lang) ?
                    $entity->lang :
                    $entity->sentence_lang;
            $text = $entity->text;
            $entity->script = $this->Transcriptions->detectScript($lang, $text);
            if ($entity->has('modified'))
                $entity->setDirty('modified', true);
        }
        return $entities;
    }

    public function autogen($lang) {
        $langs = $lang ?
                 array($lang) :
                 $this->Transcriptions->transcriptableLanguages();

        foreach ($langs as $lang) {
            $this->out("=== Proccessing sentences in language '$lang' ===");
            $this->out("Generating new transcriptions", 0);
            $proceeded = $this->allSentencesOperation('_autogen', array(
                'lang' => $lang
            ));
            $this->out();
            $this->out("$proceeded transcriptions generated.");
        }
    }

    protected function _autogen($sentences) {
        $generated = 0;
        if ($sentences) {
            $sentenceIds = Hash::extract($sentences, '{n}.id');
            $this->Transcriptions->deleteAll([
                'user_id IS' => null,
                'sentence_id IN' => $sentenceIds,
            ]);

            foreach ($sentences as $sentence) {
               $generated += $this->Transcriptions->generateAndSaveAllTranscriptionsFor($sentence);
            }
            $this->out('.', 0);
        }
        return $generated;
    }

    public function setContributionsScript($lang) {
        $langs = $lang ?
                 array($lang) :
                 $this->Transcriptions->langsInNeedOfScriptAutodetection();
        $proceeded = $this->batchOperation(
            'Contributions',
            '_setScript',
            array(
                'conditions' => array('sentence_lang IN' => $langs),
                'fields' => array('id', 'sentence_lang', 'script', 'text'),
            )
        );
        $langs = implode(', ', $langs);
        $this->out();
        $this->out("Script set for $proceeded contributions in lang(s) $langs.");
    }

    public function setSentencesScript($lang) {
        $langs = $lang ?
                 array($lang) :
                 $this->Transcriptions->langsInNeedOfScriptAutodetection();
        $proceeded = $this->allSentencesOperation('_setScript', array(
            'lang IN' => $langs,
        ));
        $langs = implode(', ', $langs);
        $this->out();
        $this->out("Script set for $proceeded sentences in lang(s) $langs.");
    }

    protected function _setScript($entities, $model) {
        $proceeded = 0;
        $entities = $this->detectTranscriptionsFor($entities);
        if ($entities) {
            $options = array(
                'validate' => true,
                'atomic' => false,
                'callbacks' => false,
            );
            if ($this->{$model}->saveMany($entities, $options))
                $proceeded += count($entities);
        }
        $this->out('.', 0);
        return $proceeded;
    }

    private function allSentencesOperation($operation, $conditions) {
        return $this->batchOperation('Sentences', $operation, array(
            'conditions' => $conditions,
            'fields' => array('id', 'lang', 'script', 'text', 'modified'),
        ));
    }

    private function die_usage() {
        $me = basename(__FILE__, '.php');
        die(
            "\nWrites transcription-related information to the database.\n\n".
            "  Usage: $me script {sentences|contributions} [lang]\n".
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
