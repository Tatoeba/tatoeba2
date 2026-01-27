<?php
namespace App\Test\TestCase\Controller;

use App\Controller\UserController;
use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;
use Cake\Utility\Security;
use Cake\Filesystem\File;

class UserControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.audios',
        'app.contributions',
        'app.favorites_users',
        'app.users',
        'app.private_messages',
        'app.sentence_comments',
        'app.sentences',
        'app.users_languages',
        'app.wiki_articles',
    ];

    private $oldPasswords = [];

    private $tmpFile = TMP.'UserControllerTest_tmpFile';

    public function setUp() {
        parent::setUp();
        $this->previousSalt = Security::getSalt();
        Security::setSalt('ze@9422#5dS?!99xx');

        $users = TableRegistry::get('Users');
        $users = $users->find()->select(['username', 'password'])->all();
        $this->oldPasswords = $users->combine('username', 'password')->toArray();
    }

    public function tearDown() {
        $file = new File($this->tmpFile);
        if ($file->exists()) {
            $file->delete();
        }
        Security::setSalt($this->previousSalt);
        parent::tearDown();
    }

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/user/profile/contributor', null, true ],
            [ '/en/user/profile/contributor', 'contributor', true ],
            [ '/en/user/profile', null, '/en/home' ],
            [ '/en/user/profile', 'contributor', '/en/user/profile/contributor' ],
            [ '/en/user/profile/nonexistent', null, '/en/users/all' ],
            [ '/en/user/edit_profile', null, '/en/users/login?redirect=%2Fen%2Fuser%2Fedit_profile' ],
            [ '/en/user/edit_profile', 'contributor', true ],
            [ '/en/user/settings', null, '/en/users/login?redirect=%2Fen%2Fuser%2Fsettings' ],
            [ '/en/user/settings', 'contributor', true ],
            [ '/en/user/language', null, '/en/users/login?redirect=%2Fen%2Fuser%2Flanguage' ],
            [ '/en/user/language', 'contributor', true ],
            [ '/en/user/language/jpn', 'contributor', true ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
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
        $this->post('/en/user/save_password', [
            'old_password' => $oldPassword,
            'new_password' => $newPassword,
            'new_password2' => $newPassword,
        ]);
        $this->assertPassword('changed', $username);
        $this->assertRedirect('/en/user/settings');
    }

    public function testSavePassword_failsIfNewPasswordIsEmpty() {
        $username = 'contributor';
        $oldPassword = '123456';
        $newPassword = '';
        $this->logInAs($username);
        $this->enableRetainFlashMessages();
        $this->post('/en/user/save_password', [
            'old_password' => $oldPassword,
            'new_password' => $newPassword,
            'new_password2' => $newPassword,
        ]);
        $this->assertPassword("didn't change", $username);
        $this->assertFlashMessage('New password cannot be empty.');
        $this->assertRedirect('/en/user/settings');
    }

    public function testSavePassword_failsIfOldPasswordDoesntMatch() {
        $username = 'contributor';
        $oldPassword = 'incorrect password';
        $newPassword = '9{FA0E;pL#R(5JllB{wHWTO;6';
        $this->logInAs($username);
        $this->post('/en/user/save_password', [
            'old_password' => $oldPassword,
            'new_password' => $newPassword,
            'new_password2' => $newPassword,
        ]);
        $this->assertPassword("didn't change", $username);
        $this->assertFlashMessage('Password error. Please try again.');
        $this->assertRedirect('/en/user/settings');
    }

    public function testSavePassword_failsIfNewPasswordDoesntMatch() {
        $username = 'contributor';
        $oldPassword = '123456';
        $this->logInAs($username);
        $this->post('/en/user/save_password', [
            'old_password' => $oldPassword,
            'new_password' => 'something',
            'new_password2' => 'something different',
        ]);
        $this->assertPassword("didn't change", $username);
        $this->assertFlashMessage('New passwords do not match.');
        $this->assertRedirect('/en/user/settings');
    }

    public function testSaveBasic_email_ok() {
        $this->logInAs('contributor');
        $this->put('/en/user/edit_profile', [
            'email' => 'contributor_newemail@example.org',
        ]);

        $this->assertRedirect('/en/user/profile/contributor');
        $this->assertFlashMessage('Profile saved.');
    }

    public function testSaveBasic_email_fail_invalid() {
        $this->enableRetainFlashMessages();
        $this->logInAs('contributor');
        $this->put('/en/user/settings', [
            'email' => 'invalid',
        ]);

        $this->assertResponseOk();
        $this->assertFlashMessage('Failed to change email address. Please enter a proper email address.');
    }

    public function testSaveBasic_email_fail_duplicate() {
        $this->enableRetainFlashMessages();
        $this->logInAs('contributor');
        $this->put('/en/user/settings', [
            'email' => 'admin@example.com',
        ]);

        $this->assertResponseOk();
        $this->assertFlashMessage('That email address already exists. Please try another.');
    }

    public function testEditProfile_birthday_ok() {
        $this->logInAs('contributor');
        $this->put('/en/user/edit_profile', [
            'birthday' => ['year' => '2109', 'month' => '01', 'day' => '23'],
        ]);

        $this->assertRedirect('/en/user/profile/contributor');
        $this->assertFlashMessage('Profile saved.');
    }

    public function testEditProfile_birthday_invalid_date() {
        $this->enableRetainFlashMessages();
        $this->logInAs('contributor');
        $this->put('/en/user/edit_profile', [
            'birthday' => ['year' => '2000', 'month' => '23', 'day' => '23'],
        ]);

        $this->assertResponseOk();
        $this->assertFlashMessage('The entered birthday is an invalid date. Please try again.');
    }

    public function testEditProfile_birthday_invalid_format() {
        $this->enableRetainFlashMessages();
        $this->logInAs('contributor');
        $this->put('/en/user/edit_profile', [
            'birthday' => ['year' => '2000', 'month' => '', 'day' => '01'],
        ]);

        $this->assertResponseOk();
        $this->assertFlashMessage('The entered birthday is incomplete. Accepted birthdays: full date, month and day, year and month, only year.');
    }

    public function testEditProfile_ignoresUnallowedFields() {
        $username = 'contributor';
        $newRole = \App\Model\Entity\User::ROLE_ADMIN;
        $this->logInAs($username);

        $this->put('/en/user/edit_profile', [
            'name' => 'Contributor',
            'country_id' => 'CL',
            'birthday' => [
                'year' => '1999',
                'month' => '01',
                'day' => '01'
            ],
            'homepage' => '',
            'description' => '',
            'role' => $newRole,
        ]);

        $users = TableRegistry::get('Users');
        $user = $users->findByUsername($username)->first();
        $this->assertNotEquals($newRole, $user->role);
    }

    public function testSaveSettings() {
        $this->enableRetainFlashMessages();
        $this->logInAs('contributor');

        $this->put('/en/user/settings', [
            'send_notifications' => '1',
            'settings' => [
                'is_public' => '1',
                'lang' => 'fra',
            ],
        ]);

        $this->assertResponseOk();
        $this->assertFlashMessage('Your settings have been saved.');
    }

    public function testSaveSettings_ignoresUnallowedFields() {
        $username = 'contributor';
        $newRole = \App\Model\Entity\User::ROLE_ADMIN;
        $this->logInAs($username);

        $this->put('/en/user/settings', [
            'send_notifications' => '1',
            'settings' => [
                'is_public' => '1',
                'lang' => 'fra',
            ],
            'role' => $newRole,
        ]);

        $users = TableRegistry::get('Users');
        $user = $users->findByUsername($username)->first();
        $this->assertNotEquals($newRole, $user->role);
    }

    private function prepareImageUpload() {
        $someImage = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAA'.
                                   'AAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
        $ok = file_put_contents($this->tmpFile, $someImage);
        $this->assertNotFalse($ok);
        return [
            'tmp_name' => $this->tmpFile,
            'error' => UPLOAD_ERR_OK,
            'name' => '1x1_black.png',
            'type' => 'image/png',
            'size' => strlen($someImage),
        ];
    }

    private function assertProfilePictureUploaded($username) {
        $image = TableRegistry::get('Users')
            ->findByUsername($username)
            ->first()
            ->image;
        $images = [
            WWW_ROOT.'img/profiles_128/'.$image,
            WWW_ROOT.'img/profiles_36/'.$image,
        ];
        foreach ($images as $image) {
            $file = new File($image);
            $this->assertFileExists($image);
            $file->delete();
        }
    }

    public function testSaveImage() {
        require __DIR__ . '/UserControllerTestFakeFunctions.php';
        $username = 'contributor';
        $this->logInAs($username);
        $this->post('/en/user/save_image', [
            'image' => $this->prepareImageUpload()
        ]);
        $this->assertNoFlashMessage();
        $this->assertRedirect("/en/user/profile/$username");
        $this->assertProfilePictureUploaded($username);
    }

    public function testRemoveImage() {
        $users = TableRegistry::get('Users');
        $contributor = $users->get(4);
        $images = [
            WWW_ROOT.'img/profiles_128/'.$contributor->image,
            WWW_ROOT.'img/profiles_36/'.$contributor->image,
        ];
        foreach ($images as $image) {
            $file = new File($image, true);
            $file->close();
            $this->assertFileExists($image);
        }

        $this->logInAs('contributor');
        $this->post('/en/user/remove_image');
        $this->assertRedirect('/en/user/profile/contributor');

        $contributor = $users->get(4);
        $this->assertEmpty($contributor->image);
        foreach ($images as $image) {
            $this->assertFileNotExists($image);
        }
    }

    public function testAcceptNewTermsOfUser_asGuest() {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/en/user/accept_new_terms_of_use', [
            'settings' => [ 'new_terms_of_use' => true ],
        ]);
        $this->assertResponseCode(404);
    }

    public function testAcceptNewTermsOfUser_asMember() {
        $this->logInAs('contributor');
        $this->addHeader('Referer', 'https://example.net/referer');
        $this->post('/en/user/accept_new_terms_of_use', [
            'settings' => [ 'new_terms_of_use' => true ],
        ]);
        $this->assertRedirect('https://example.net/referer');
    }
}
