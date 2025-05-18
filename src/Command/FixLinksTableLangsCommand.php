<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class FixLinksTableLangsCommand extends Command
{
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Links');
        $this->loadModel('Sentences');
    }

    public function execute(Arguments $args, ConsoleIo $io) {
        foreach (['sentence', 'translation'] as $column) {
            $io->out(format(
                'Processing field {column}_lang...',
                compact('column')
            ));
            $ids = $this->findWrongLanguageLinks($column);
            $io->out(format(
                '{column}_lang: found {count} sentence(s) having incorrect language.',
                [ 'count' => $ids->count(), 'column' => $column ]
            ));
            $total = $this->fixLinksLanguage($column, $ids);
            $io->out(format(
                '{column}_lang: fixed {total} row(s) in links table.',
                compact('column', 'total')
            ));
        }
    }

    private function fixLinksLanguage(string $column, $sentenceIds) {
        $field = "${column}_id";
        $count = 0;
        foreach ($sentenceIds as $id) {
            $correctLang = $this->Sentences->get($id)->lang;
            $count += $this->Links->updateAll(
                [ "${column}_lang" => $correctLang ],
                [ $field => $id ]
            );
        }
        return $count;
    }

    private function findWrongLanguageLinks($column) {
        return $this->Links->find()
            ->enableHydration(false)
            ->select([
                'id' => "${column}_id",
            ])
            ->distinct()
            ->join([
                'table' => 'sentences',
                'alias' => 's',
                'conditions' => "s.id = Links.${column}_id",
            ])
            ->where(['not' => ["s.lang <=> Links.${column}_lang"]])
            ->all()
            ->extract('id');
    }
}
