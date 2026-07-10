<?php
use Migrations\BaseMigration;

class UserLangIndex extends BaseMigration
{
    public function up()
    {
        $table = $this->table('sentences');
        $table->addIndex(['user_id', 'lang'], ['name' => 'user_lang_idx'])
              ->removeIndexByName('user_id')
              ->update();
    }

    public function down()
    {
        $table = $this->table('sentences');
        $table->addIndex(['user_id'], ['name' => 'user_id'])
              ->removeIndexByName('user_lang_idx')
              ->update();
    }
}
