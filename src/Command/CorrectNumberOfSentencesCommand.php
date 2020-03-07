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
        $query = $this->SentencesSentencesLists->find();
        $query->select([
            'sentences_list_id',
            'count' => $query->func()->count('*')
        ])
        ->group('sentences_list_id');
        foreach ($query as $listAndCount) {
            $sentencesList = $this->SentencesLists->get($listAndCount->sentences_list_id);
            $sentencesList->numberOfSentences = $listAndCount->count;
            $this->SentencesLists->save($sentencesList);
        }
    }
}
