<?php
use Migrations\AbstractMigration;

class AddPublicplus extends AbstractMigration
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
        $table = $this->table('sentences_lists');

        $table->changeColumn('visibility', 'enum', [
            'after' => 'modified',
            'default' => 'unlisted',
            'null' => false,
            'values' =>[
                'private',
                'unlisted',
                'listed',
                'public']
            ])->update();
    }
}
