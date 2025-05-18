<?php
use Migrations\AbstractMigration;

class UpdateListVisibility extends AbstractMigration
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
        $builder = $this->getQueryBuilder();
        $builder
            ->update('sentences_lists')
            ->set('visibility', 'listed')
            ->where(['visibility' => 'public'])
            ->execute();
    }
}
