<?php
use Migrations\AbstractMigration;

class SentencesReplaceHashWithKey extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('sentences');
        $table->addIndex(['text', 'lang'], ['name' => 'text_lang_idx'])
              ->removeIndexByName('hash')
              ->removeIndexByName('dedup_idx')
              ->removeColumn('hash')
              ->update();
    }
}
