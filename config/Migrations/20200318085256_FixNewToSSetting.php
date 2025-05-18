<?php
use Migrations\AbstractMigration;

class FixNewToSSetting extends AbstractMigration
{
    public function up()
    {
        $this->getAdapter()->beginTransaction();

        $io = new \Cake\Console\ConsoleIo();
        $args = [];
        $cmd = new \App\Command\FixNewUsersTOSSettingCommand();
        $exitCode = $cmd->run($args, $io);

        $this->getAdapter()->commitTransaction();
    }

    public function down()
    {
    }
}
