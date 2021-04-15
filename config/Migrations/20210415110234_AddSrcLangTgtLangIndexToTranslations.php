<?php
use Migrations\AbstractMigration;

class AddSrcLangTgtLangIndexToTranslations extends AbstractMigration
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
        $table = $this->table('sentences_translations');
        $table->addIndex(['sentence_lang', 'translation_lang'], ['name' => 'sentence_lang_translation_lang_idx'])
              ->removeIndexByName('sentence_lang')
              ->update();
    }
}
