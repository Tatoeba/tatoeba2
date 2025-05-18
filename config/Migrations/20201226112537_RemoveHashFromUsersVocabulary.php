<?php
use Migrations\AbstractMigration;

class RemoveHashFromUsersVocabulary extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users_vocabulary');
        $table->removeColumn('hash')
              ->update();
    }
}
