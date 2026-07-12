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

use App\Model\Sentence;
use App\Model\Transcription;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Utility\Hash;

class TranscriptionsCommand extends Command
{
    use BatchOperationTrait;

    private ConsoleIo $io;
    private $Transcriptions;

    public function initialize(): void {
        parent::initialize();
        $this->Transcriptions = $this->fetchTable('Transcriptions');
    }

    protected function out() {
        $this->io->out(...func_get_args());
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
            $this->out("");
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
        $this->out("");
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
        $this->out("");
        $this->out("Script set for $proceeded sentences in lang(s) $langs.");
    }

    protected function _setScript($entities, $modelName) {
        $proceeded = 0;
        $entities = $this->detectTranscriptionsFor($entities);
        if ($entities) {
            $options = array(
                'validate' => true,
                'atomic' => false,
                'callbacks' => false,
            );
            if ($this->fetchTable($modelName)->saveMany($entities, $options))
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

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        $parser
            ->setDescription('Write transcription-related information to the database.')
            ->addArgument('operation', [
                'help' => 'Operation to perform',
                'choices' => [
                    'autodetect_sentences_script',
                    'autodetect_contributions_script',
                    'autogen_transcriptions_text'
                ],
                'required' => true,
            ])
            ->addArgument('language', [
                'help' => 'Limit affected rows to this language (three-letter ISO code). If not specified, the operation will affect ALL relevant languages.',
            ])
            ->addOption('batchSize', [
                'help' => "Size of batch of rows to operate on (default={$this->batchOperationSize})",
            ])
            ->setEpilog([
                "Description of operations:",
                "",
                "autodetect_*: fills 'script' column in the sentences",
                "              or contributions table.",
                "   autogen_*: removes autogenerated transcriptions and",
                "              regenerate them, for all the sentences."
            ]);

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $this->io = $io;
        $this->batchOperationSize = (int)$args->getOption('batchSize') ?: 1000;
        $lang = $args->getArgument('language');
        $operation = $args->getArgument('operation');
        switch ($operation) {
            case 'autodetect_sentences_script':
                $this->setSentencesScript($lang);
                break;
            case 'autodetect_contributions_script':
                $this->setContributionsScript($lang);
                break;
            case 'autogen_transcriptions_text':
                $this->autogen($lang);
                break;
            default:
                throw new \RuntimeException(sprintf('Unexpected operation: `%s`', $operation));
        }
        return static::CODE_SUCCESS;
    }
}
