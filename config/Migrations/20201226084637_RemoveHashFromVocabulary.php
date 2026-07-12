<?php
use Migrations\BaseMigration;

class RemoveHashFromVocabulary extends BaseMigration
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
