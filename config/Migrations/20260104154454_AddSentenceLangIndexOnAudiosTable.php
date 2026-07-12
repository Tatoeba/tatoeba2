<?php
use Migrations\BaseMigration;

class AddSentenceLangIndexOnAudiosTable extends BaseMigration
{
    public function up()
    {
        $table = $this->table('audios');
        if (!$table->hasIndex(['sentence_lang'])) {
            $table->addIndex(['sentence_lang'], ['name' => 'sentence_lang_idx'])
                  ->update();
        }
    }

    public function down()
    {
        $table = $this->table('audios');
        $table->removeIndexByName('sentence_lang_idx')
              ->update();
    }
}
