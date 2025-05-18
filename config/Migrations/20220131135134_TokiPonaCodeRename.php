<?php

use Cake\Core\Configure;
use Migrations\AbstractMigration;

class TokiPonaCodeRename extends AbstractMigration
{
    private $langColumns = [
        'contributions' => ['sentence_lang', 'translation_lang'],
        'contributions_stats' => ['lang'],
        'languages' => ['code'],
        'last_contributions' => ['sentence_lang', 'translation_lang'],
        'reindex_flags' => ['lang'],
        'sentence_comments' => ['lang'],
        'sentences' => ['lang'],
        'sentences_translations' => ['sentence_lang', 'translation_lang'],
        'users_languages' => ['language_code'],
        'vocabulary' => ['lang'],
    ];

    private $oldCode = 'toki';
    private $newCode = 'tok';

    private function updateCode($from, $to) {
        foreach ($this->langColumns as $table => $columns) {
            foreach ($columns as $column) {
                $this->getQueryBuilder()
                     ->update($table)
                     ->set($column, $to)
                     ->where([$column => $from])
                     ->execute();
            }
        }
        $audioBasePath = Configure::read('Recordings.path');
        if (is_dir($audioBasePath.DS.$from)) {
            rename($audioBasePath.DS.$from, $audioBasePath.DS.$to);
        }
    }

    public function up() {
        $this->updateCode($this->oldCode, $this->newCode);
    }

    public function down() {
        $this->updateCode($this->newCode, $this->oldCode);
    }
}
