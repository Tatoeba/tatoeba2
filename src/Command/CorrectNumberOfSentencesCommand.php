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
            $sentencesList = $this->SentencesLists->get($listAndCount->sentences_list_id);
            $sentencesList->numberOfSentences = $listAndCount->count;
            $this->SentencesLists->save($sentencesList);
        }

        // The lists that do not appear have numberOfSentences set to 0
        $subquery = $this->SentencesSentencesLists->find()->select('sentences_list_id');
        $listsWithNoSentenceQuery = $this->SentencesLists->find('all', array(
            'conditions' => array('id NOT IN' => $subquery)
        ));

        foreach($listsWithNoSentenceQuery as $sentencesList){
            $sentencesList->numberOfSentences = 0;
            $this->SentencesLists->save($sentencesList);
        }
    }
}
