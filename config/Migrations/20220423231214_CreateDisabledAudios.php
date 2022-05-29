<?php
use Migrations\AbstractMigration;

class CreateDisabledAudios extends AbstractMigration
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
        $table = $this->table('disabled_audios', [
            'id' => false,
            'primary_key' => 'id',
        ]);

        $table->addColumn('id', 'integer', [
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('sentence_id', 'integer', [
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('user_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addColumn('external', 'string', [
            'default' => null,
            'limit' => 500,
            'null' => true,
        ]);
        $table->addColumn('created', 'datetime', [
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'null' => false,
        ]);
        $table->addIndex('sentence_id');
        $table->create();
    }
}
