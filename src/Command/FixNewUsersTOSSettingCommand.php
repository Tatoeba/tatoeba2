<?php
namespace App\Command;

class FixNewUsersTOSSettingCommand extends \Cake\Console\Command
{
    public function execute(\Cake\Console\Arguments $args, \Cake\Console\ConsoleIo $io)
    {
        $this->loadModel('Users');
        $users = $this->Users->find()
            ->select(['id', 'settings'])
            ->where([
                "since > '2019-01-20'", // date the new ToS came into effect
                'settings like :falsePattern',
            ])
            ->bind(':falsePattern', '%"new_terms_of_use":false%', 'string')
            ->all();
        foreach ($users as $user) {
            $user->set('settings', ['new_terms_of_use' => '1']);
        }
        $this->Users->saveMany($users);
    }
}
