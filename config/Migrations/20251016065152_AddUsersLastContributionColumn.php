<?php
use Migrations\AbstractMigration;

class AddUsersLastContributionColumn extends AbstractMigration
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
        $table = $this->table('users');
        $table->addColumn('last_contribution', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->update();
    }
}
