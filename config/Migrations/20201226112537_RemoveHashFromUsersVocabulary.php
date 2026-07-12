<?php
use Migrations\BaseMigration;

class RemoveHashFromUsersVocabulary extends BaseMigration
{
    public function change()
    {
        $table = $this->table('users_vocabulary');
        $table->removeColumn('hash')
              ->update();
    }
}
