<?php
use Migrations\BaseMigration;

class UpdateListVisibility extends BaseMigration
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
        $builder = $this->getUpdateBuilder();
        $builder
            ->update('sentences_lists')
            ->set('visibility', 'listed')
            ->where(['visibility' => 'public'])
            ->execute();
    }
}
