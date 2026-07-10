<?php
use Migrations\BaseMigration;

class AddUserIdIndexOnAudiosTable extends BaseMigration
{
    public function up()
    {
        $table = $this->table('audios');
        if (!$table->hasIndex(['user_id'])) {
            $table->addIndex(['user_id'], ['name' => 'user_id_idx'])
                  ->update();
        }
    }

    public function down()
    {
        $table = $this->table('audios');
        $table->removeIndexByName('user_id_idx')
              ->update();
    }
}
