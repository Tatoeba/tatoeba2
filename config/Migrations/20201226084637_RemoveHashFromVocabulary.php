<?php
use Migrations\AbstractMigration;

class RemoveHashFromVocabulary extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('vocabulary');
        $table->removeColumn('hash')
              ->addIndex(['text', 'lang'], [
                  'unique' => true,
                  'name' => 'text_lang_idx',
              ])
              ->update();
    }
}
