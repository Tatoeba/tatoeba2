<?php
use Migrations\BaseMigration;

class AddTypeToReindexFlags extends BaseMigration
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
