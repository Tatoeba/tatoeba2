<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;

class CorrectNumberOfSentencesCommand extends Command
{
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('SentencesLists');
        $this->loadModel('SentencesSentencesLists');
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        // Correct lists that appear in the sentences_sentences_lists table
        $countQuery = $this->SentencesSentencesLists->find();
        $countQuery->select([
            'sentences_list_id',
            'count' => $countQuery->func()->count('*')
        ])
        ->group('sentences_list_id');
        foreach ($countQuery as $listAndCount) {
            try {
                $sentencesList = $this->SentencesLists->get($listAndCount->sentences_list_id);
                $sentencesList->setDirty('modified', true);
                $sentencesList->numberOfSentences = $listAndCount->count;
                $this->SentencesLists->save($sentencesList);
            } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            }
        }

        // The lists that do not appear have numberOfSentences set to 0
        $subquery = $this->SentencesSentencesLists->find()->select('sentences_list_id');
        $this->SentencesLists->updateAll(['numberOfSentences' => 0], ['id NOT IN' => $subquery]);
    }
}
