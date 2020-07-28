<?php
namespace App\Test\TestCase\Command;

use App\Command\FixNewUsersTOSSettingCommand;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

class FixNewUsersTOSSettingCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    public $fixtures = [
        'app.UsersTosTest',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->useCommandRunner();
    }

    private function extractTosSettings() {
        $users = TableRegistry::getTableLocator()->get('Users');
        return $users->find()
                     ->select(['username', 'settings'])
                     ->combine('username', 'settings.new_terms_of_use')
                     ->toArray();
    }

    public function testExecute()
    {
        $this->assertEquals($this->extractTosSettings(), [
            'old_tos_no_setting' => '1',
            'old_tos_setting_fals' => false,
            'old_tos_setting_1' => '1',
            'new_tos_no_setting' => '1',
            'new_tos_setting_fals' => false,
            'new_tos_setting_1' => '1',
        ]);

        $this->exec('fix_new_users_t_o_s_setting');

        $this->assertEquals($this->extractTosSettings(), [
            'old_tos_no_setting' => '1',
            'old_tos_setting_fals' => false,
            'old_tos_setting_1' => '1',
            'new_tos_no_setting' => '1',
            'new_tos_setting_fals' => '1',
            'new_tos_setting_1' => '1',
        ]);
    }
}
