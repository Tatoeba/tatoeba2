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
            $ids = $this->findWrongLanguageLinks($column);
            $this->fixLinksLanguage($column, $ids);
        }
    }

    private function fixLinksLanguage(string $column, $sentenceIds) {
        $field = "${column}_id";
        foreach ($sentenceIds as $id) {
            $correctLang = $this->Sentences->get($id)->lang;
            $this->Links->query()->update()
                 ->set(["${column}_lang" => $correctLang])
                 ->where([$field => $id])
                 ->execute();
        }
    }

    private function findWrongLanguageLinks($column) {
        return $this->Links->find()
            ->enableHydration(false)
            ->select([
                'id' => "${column}_id",
            ])
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
