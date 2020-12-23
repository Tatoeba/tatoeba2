<?php
use Migrations\AbstractMigration;

class SentencesReplaceHashWithUniqueKey extends AbstractMigration
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
        $table->addIndex(['lang', 'text'], [
            'unique' => true,
            'name' => 'unique_lang_text'
        ])
        ->removeIndexByName('hash')
        ->removeIndexByName('dedup_idx')
        ->removeColumn('hash')
        ->update();
    }
}
