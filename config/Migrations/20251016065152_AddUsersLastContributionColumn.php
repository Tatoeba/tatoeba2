<?php
use Migrations\AbstractMigration;

class AddUsersLastContributionColumn extends AbstractMigration
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
        $table = $this->table('users');
        $table->addColumn('last_contribution', 'datetime', [
            'default' => null,
            'null' => true,
            'after' => 'last_time_active'
        ]);
        $table->update();

        if ($this->isMigratingUp()) {
            $this->execute(
                "update users u 
                    join (
                        select user_id, max(datetime) as dt from contributions where 
                            (action = 'insert' or action = 'update') 
                            and type = 'sentence'
                            group by user_id
                    ) c 
                    on c.user_id = u.id set u.last_contribution = c.dt"
            );
        }
    }
}
