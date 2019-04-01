<?php
use Migrations\AbstractMigration;

class CreateExports extends AbstractMigration
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
        $table = $this->table('exports');
        $table->addColumn('name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('description', 'text', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('url', 'string', [
            'default' => null,
            'limit' => 2048,
            'null' => true,
        ]);
        $table->addColumn('filename', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->addColumn('generated', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('status', 'enum', [
            'default' => null,
            'null' => false,
            'values' => ['queued', 'exported', 'online'],
        ]);
        $table->addColumn('queued_job_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addColumn('user_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->create();
    }
}
