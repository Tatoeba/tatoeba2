<?php
namespace App\Test\TestCase\Controller;

use App\Controller\UserController;
use App\Test\TestCase\Controller\LogInAsTrait;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;
use Cake\Utility\Security;

class UserControllerTest extends IntegrationTestCase
{
    use LogInAsTrait;

    public $fixtures = [
        'app.aros',
        'app.acos',
        'app.aros_acos',
        'app.users',
        'app.groups',
        'app.users_languages'
    ];

    private $oldPasswords = [];

    public function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
        Security::setSalt('ze@9422#5dS?!99xx');

        $users = TableRegistry::get('Users');
        $users = $users->find()->select(['username', 'password'])->all();
        $this->oldPasswords = $users->combine('username', 'password')->toArray();
    }

    private function assertPassword($what, $username) {
        $users = TableRegistry::get('Users');
        $user = $users->findByUsername($username)->first();
        $currentPassword = $user->password;
        $oldPassword = $this->oldPasswords[$username];
        if ($oldPassword) {
            $message = "Password of '$username' $what";
            switch ($what) {
            case "didn't change":
                $this->assertEquals($oldPassword, $currentPassword, $message);
                return;
            case "changed":
                $this->assertNotEquals($oldPassword, $currentPassword, $message);
                return;
            }
        }
        $this->fail("Failed to assert that password of user '$username' $what");
    }

    public function testSavePassword_changesPassword() {
        $username = 'contributor';
        $oldPassword = '123456';
        $newPassword = '9{FA0E;pL#R(5JllB{wHWTO;6';
        $this->logInAs($username);
        $this->post('/eng/user/save_password', [
            'old_password' => $oldPassword,
            'new_password' => $newPassword,
            'new_password2' => $newPassword,
        ]);
        $this->assertPassword('changed', $username);
    }

    public function testSavePassword_failsIfNewPasswordIsEmpty() {
        $username = 'contributor';
        $oldPassword = '123456';
        $newPassword = '';
        $this->logInAs($username);
        $this->enableRetainFlashMessages();
        $this->post('/eng/user/save_password', [
            'old_password' => $oldPassword,
            'new_password' => $newPassword,
            'new_password2' => $newPassword,
        ]);
        $this->assertPassword("didn't change", $username);
        $this->assertFlashMessage('New password cannot be empty.');
    }

    public function testSavePassword_failsIfOldPasswordDoesntMatch() {
        $username = 'contributor';
        $oldPassword = 'incorrect password';
        $newPassword = '9{FA0E;pL#R(5JllB{wHWTO;6';
        $this->logInAs($username);
        $this->post('/eng/user/save_password', [
            'old_password' => $oldPassword,
            'new_password' => $newPassword,
            'new_password2' => $newPassword,
        ]);
        $this->assertPassword("didn't change", $username);
        $this->assertFlashMessage('Password error. Please try again.');
    }

    public function testSavePassword_failsIfNewPasswordDoesntMatch() {
        $username = 'contributor';
        $oldPassword = '123456';
        $this->logInAs($username);
        $this->post('/eng/user/save_password', [
            'old_password' => $oldPassword,
            'new_password' => 'something',
            'new_password2' => 'something different',
        ]);
        $this->assertPassword("didn't change", $username);
        $this->assertFlashMessage('New passwords do not match.');
    }

    public function testSaveBasic_changingEmailUpdatesAuthData() {
        $username = 'contributor';
        $newEmail = 'contributor_newemail@example.org';
        $this->logInAs($username);
        $this->post('/eng/user/save_basic', [
            'email' => $newEmail,
        ]);
        $this->assertEquals($this->_controller->Auth->user('username'), $username);
        $this->assertEquals($this->_controller->Auth->user('email'), $newEmail);
    }

    public function testSaveBasic_ignoresUnallowedFields() {
        $username = 'contributor';
        $newGroup = 1;
        $this->logInAs($username);

        $this->post('/eng/user/save_basic', [
            'name' => 'Contributor',
            'country_id' => 'CL',
            'birthday' => [
                'year' => '1999',
                'month' => '01',
                'day' => '01'
            ],
            'homepage' => '',
            'description' => '',
            'group_id' => $newGroup,
        ]);

        $users = TableRegistry::get('Users');
        $user = $users->findByUsername($username)->first();
        $this->assertNotEquals($newGroup, $user->group_id);
    }

    public function testSaveSettings_ignoresUnallowedFields() {
        $username = 'contributor';
        $newGroup = 1;
        $this->logInAs($username);

        $this->post('/eng/user/save_settings', [
            'send_notifications' => '1',
            'settings' => [
                'is_public' => '1',
                'lang' => 'fra',
            ],
            'group_id' => $newGroup,
        ]);

        $users = TableRegistry::get('Users');
        $user = $users->findByUsername($username)->first();
        $this->assertNotEquals($newGroup, $user->group_id);
    }
}
