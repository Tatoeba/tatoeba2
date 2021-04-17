<?php
use Migrations\AbstractMigration;

class Issue2120 extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        // sentences_lists.name to utf8mb4_general_ci
        $sl = $this->table('sentences_lists');
        $sl->changeColumn('name', 'string', ['collation' => 'utf8mb4_general_ci'] )->save();
    }
}
