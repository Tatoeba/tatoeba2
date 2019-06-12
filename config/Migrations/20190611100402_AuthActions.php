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
        $rolesCase = $builder->newExpr()->addCase(
            [
                $builder->newExpr()->add(['group_id' => 1]),
                $builder->newExpr()->add(['group_id' => 2]),
                $builder->newExpr()->add(['group_id' => 3]),
                $builder->newExpr()->add(['group_id' => 4]),
                $builder->newExpr()->add(['group_id' => 5]),
                $builder->newExpr()->add(['group_id' => 6]),
            ],
            [
                'admin',
                'corpus_maintainer',
                'advanced_contributor',
                'contributor',
                'inactive',
                'spammer',
            ],
            [ 'string', 'string', 'string', 'string', 'string', 'string' ]
        );
        $builder
            ->update('users')
            ->set('role', $rolesCase)
            ->execute();

        $table->deleteColumn('group_id')->save();
    }
}
