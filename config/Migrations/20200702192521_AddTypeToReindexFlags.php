<?php
use Migrations\AbstractMigration;

class AddTypeToReindexFlags extends AbstractMigration
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
        $table = $this->table('reindex_flags');
        $table->addColumn('type', 'enum', [
            'values' => ['change', 'removal'],
            'null' => false,
        ]);
        $table->update();
    }
}
