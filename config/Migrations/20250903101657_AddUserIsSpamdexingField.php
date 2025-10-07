<?php
use Migrations\AbstractMigration;

class AddUserIsSpamdexingField extends AbstractMigration
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
        $table->addColumn('is_spamdexing', 'boolean', [
            'null' => true,
            'default' => null,
        ]);
        $table->update();
    }
}
