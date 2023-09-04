<?php
use Migrations\AbstractMigration;

class Sessions extends AbstractMigration
{
    public function up()
    {
        $this->table('sessions', [
                'id' => false,
                'primary_key' => ['id'],
            ])
            ->addColumn('id', 'string', [
                'default' => null,
                'limit' => 40,
                'null' => false,
                'encoding' => 'ascii',
                'collation' => 'ascii_bin',
            ])
            ->addColumn('created', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
                'update' => 'CURRENT_TIMESTAMP',
            ])
            ->addColumn('data', 'blob', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('expires', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
                'signed' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
                'signed' => false,
            ])
            ->create();
    }

    public function down()
    {
        $this->table('sessions')->drop()->save();
    }
}

