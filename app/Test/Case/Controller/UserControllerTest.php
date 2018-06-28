<?php
App::uses('UserController', 'Controller');

class UserControllerTest extends ControllerTestCase {

    public $fixtures = array(
        'app.aro',
        'app.aco',
        'app.aros_aco',
        'app.user',
        'app.sentence',
    );

    private $oldPasswords = array();

    public function setUp() {
        Configure::write('Acl.database', 'test');
    }

    public function startTest($method) {
        $this->controller = $this->generate('User', array(
            'components' => array(
                'Auth' => array('user')
            )
        ));

        $users = $this->controller->User->find('all', array(
            'fields' => array('username', 'password'),
        ));
        $this->oldPasswords = Set::combine($users, '{n}.User.username', '{n}.User.password');
    }

    public function tearDown() {
        unset($this->controller);
    }

    private function logInAs($username) {
        $user = $this->controller->User->find('first', array(
            'conditions' => array('username' => $username),
        ));
        $this->controller->Auth
            ->staticExpects($this->any())
            ->method('user')
            ->will($this->returnCallback(
                function () use ($user) {
                    $args = func_get_args();
                    if (isset($args[0])) {
                        return $user['User'][ $args[0] ];
                    } else {
                        return $user['User'];
                    }
                }
            ));
    }

    private function assertPassword($what, $username) {
        $user = $this->controller->User->findByUsername($username);
        $currentPassword = $user['User']['password'];
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

    private function assertFlashMessage($message) {
        $flash = $this->controller->Session->read('Message.flash');
        $this->assertEquals($message, $flash['message'], "Flash message equals '$message'");
    }

    public function testSavePassword_changesPassword() {
        $username = 'contributor';
        $oldPassword = '123456';
        $newPassword = '9{FA0E;pL#R(5JllB{wHWTO;6';
        $this->logInAs($username);
        $this->testAction('/eng/save_password', array(
            'data' => array(
                'User' => array(
                    'old_password' => $oldPassword,
                    'new_password' => $newPassword,
                    'new_password2' => $newPassword,
                )
            )
        ));
        $this->assertPassword('changed', $username);
    }

    public function testSavePassword_failsIfNewPasswordIsEmpty() {
        $username = 'contributor';
        $oldPassword = '123456';
        $newPassword = '';
        $this->logInAs($username);
        $this->testAction('/eng/save_password', array(
            'data' => array(
                'User' => array(
                    'old_password' => $oldPassword,
                    'new_password' => $newPassword,
                    'new_password2' => $newPassword,
                )
            )
        ));
        $this->assertPassword("didn't change", $username);
        $this->assertFlashMessage('New password cannot be empty.');
    }

    public function testSavePassword_failsIfOldPasswordDoesntMatch() {
        $username = 'contributor';
        $oldPassword = 'incorrect password';
        $newPassword = '9{FA0E;pL#R(5JllB{wHWTO;6';
        $this->logInAs($username);
        $this->testAction('/eng/save_password', array(
            'data' => array(
                'User' => array(
                    'old_password' => $oldPassword,
                    'new_password' => $newPassword,
                    'new_password2' => $newPassword,
                )
            )
        ));
        $this->assertPassword("didn't change", $username);
        $this->assertFlashMessage('Password error. Please try again.');
    }

    public function testSavePassword_failsIfNewPasswordDoesntMatch() {
        $username = 'contributor';
        $oldPassword = '123456';
        $this->logInAs($username);
        $this->testAction('/eng/save_password', array(
            'data' => array(
                'User' => array(
                    'old_password' => $oldPassword,
                    'new_password' => 'something',
                    'new_password2' => 'something different',
                )
            )
        ));
        $this->assertPassword("didn't change", $username);
        $this->assertFlashMessage('New passwords do not match.');
    }
}
