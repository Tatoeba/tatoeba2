<?php
use Migrations\AbstractMigration;

class AllowUnknownDateAudios extends AbstractMigration
{
    private function set_nullable_datetime(bool $nullable)
    {
        foreach (['audios', 'disabled_audios'] as $table) {
            foreach (['created', 'modified'] as $column) {
                $this->table($table)
                    ->changeColumn($column, 'datetime', [
                        'null' => $nullable,
                    ])
                    ->update();
                if ($nullable) {
                    $this->getQueryBuilder()
                        ->update($table)
                        ->set($column, null)
                        ->where([$column => '0000-00-00 00:00:00'])
                        ->execute();
                }
            }
        }
    }

    public function up()
    {
        $this->set_nullable_datetime(true);
    }

    public function down()
    {
        $this->set_nullable_datetime(false);
    }
}
