<?php
use Migrations\AbstractMigration;

class AudioEnabledField extends AbstractMigration
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
	$table = $this->table('audios');
        $table->addColumn('enabled', 'boolean', [
	    'after' => 'user_id',
	    'null' => false,
	    'default' => true,
        ]);
        $table->update();
    }
}
