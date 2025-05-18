<?php
use Migrations\AbstractMigration;

class RemoveIPsFromContributions extends AbstractMigration
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
        $table = $this->table('contributions');
        $table->removeColumn('ip');
        $table->update();

        $table = $this->table('last_contributions');
        $table->removeColumn('ip');
        $table->update();
    }
}
