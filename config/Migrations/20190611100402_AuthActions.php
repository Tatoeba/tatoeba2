<?php
use Migrations\AbstractMigration;

class AuthActions extends AbstractMigration
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
        $table = $this->table('users');
        $table->addColumn('role', 'enum', [
            'after' => 'email',
            'default' => null,
            'null' => false,
            'values' => [
                'admin',
                'corpus_maintainer',
                'advanced_contributor',
                'contributor',
                'inactive',
                'spammer',
            ],
        ])->save();

        $builder = $this->getQueryBuilder();
        $rolesCase = $builder->newExpr()->case()
            ->when(['group_id' => 1])->then('admin')
            ->when(['group_id' => 2])->then('corpus_maintainer')
            ->when(['group_id' => 3])->then('advanced_contributor')
            ->when(['group_id' => 4])->then('contributor')
            ->when(['group_id' => 5])->then('inactive')
            ->when(['group_id' => 6])->then('spammer');
        $builder
            ->update('users')
            ->set('role', $rolesCase)
            ->execute();

        $table->removeColumn('group_id')->save();

        $this->table('acos')->drop()->save();
        $this->table('aros')->drop()->save();
        $this->table('aros_acos')->drop()->save();
        $this->table('groups')->drop()->save();
    }
}
