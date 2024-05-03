<?php
namespace App\Test\TestCase\Controller;

use App\Model\Entity\User;
use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\IntegrationTestCase;
use Cake\Utility\Security;

class UsersControllerTest extends IntegrationTestCase {
    use EmailTrait,
        TatoebaControllerTestTrait;

    public $fixtures = [
        'app.Contributions',
        'app.Users',
        'app.UsersLanguages',
        'app.LastContributions',
        'app.PrivateMessages',
        'app.SentenceComments',
        'app.Sentences',
        'app.Walls',
        'app.WikiArticles',
    ];

    public function setUp() {
        parent::setUp();

        $this->previousSalt = Security::getSalt();
        Security::setSalt('ze@9422#5dS?!99xx');
        $this->enableCsrfToken();
        $this->enableSecurityToken();
    }

    public function tearDown() {
        Security::setSalt($this->previousSalt);
    }

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/users/index', null, '/en/users/login?redirect=%2Fen%2Fusers%2Findex' ],
            [ '/en/users/index', 'contributor', '/' ],
            [ '/en/users/index', 'advanced_contributor', '/' ],
            [ '/en/users/index', 'corpus_maintainer', '/' ],
            [ '/en/users/index', 'admin', '/en/users/all' ],
            [ '/en/users/edit/1', null, '/en/users/login?redirect=%2Fen%2Fusers%2Fedit%2F1' ],
            [ '/en/users/edit/1', 'contributor', '/' ],
            [ '/en/users/edit/1', 'advanced_contributor', '/' ],
            [ '/en/users/edit/1', 'corpus_maintainer', '/' ],
            [ '/en/users/edit/1', 'admin', true ],
            [ '/en/users/edit/999999999999999', 'admin', '/en/users/index' ],
            [ '/en/users/delete/1', null, '/en/users/login?redirect=%2Fen%2Fusers%2Fdelete%2F1' ],
            [ '/en/users/delete/1', 'contributor', '/' ],
            [ '/en/users/delete/1', 'advanced_contributor', '/' ],
            [ '/en/users/delete/1', 'corpus_maintainer', '/' ],
            [ '/en/users/delete/1', 'admin', '/en/users/index' ],
            [ '/en/users/delete/999999999999999', 'admin', '/en/users/index' ],
            [ '/en/users/login', null, true ],
            [ '/en/users/login', 'contributor', '/' ],
            [ '/en/users/check_login', null, '/en/users/login' ],
            [ '/en/users/logout', null, '/en/users/login' ], // TODO we might want not to redirect to login page when trying to access the logout page as a guest
            [ '/en/users/logout', 'contributor', '/en/users/login' ],
            [ '/en/users/register', null, true ],
            [ '/en/users/register', 'contributor', '/' ],
            [ '/en/users/new_password', null, true ],
            [ '/en/users/new_password', 'contributor', true ],
            [ '/en/users/show/1', null, true ],
            [ '/en/users/show/1', 'contributor', true ],
            [ '/en/users/all', null, true ],
            [ '/en/users/all', 'contributor', true ],
            [ '/en/users/check_username/foobar', null, true ],
            [ '/en/users/check_username/foobar', 'contributor', true ],
            [ '/en/users/check_email/foobar@example.net', null, true ],
            [ '/en/users/check_email/foobar@example.net', 'contributor', true ],
            [ '/en/users/for_language', null, true ],
            [ '/en/users/for_language', 'contributor', true ],
            [ '/en/users/for_language/jav', null, true ],
            [ '/en/users/for_language/jav', 'contributor', true ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testUsersControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public function testSearch_found() {
        $this->post('/en/users/search', [ 'search_username' => 'contributor' ]);
        $this->assertRedirect('/en/users/show/4');
    }

    public function testSearch_notFound() {
        $this->post('/en/users/search', [ 'search_username' => 'non existent' ]);
        $this->assertRedirect('/en/users/all/');
    }

    public function testCheckLogin_correctLoginAndPasswordV0() {
        $this->post('/en/users/check_login', [
            'username' => 'contributor',
            'password' => '123456',
            'rememberMe' => 0,
        ]);
        $this->assertSession('contributor', 'Auth.User.username');
        $this->assertRedirect('/');
    }

    public function testCheckLogin_correctLoginAndincorrectPasswordV0() {
        $this->post('/en/users/check_login', [
            'username' => 'contributor',
            'password' => 'this_is_incorrect',
            'rememberMe' => 0,
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertRedirect('/en/users/login');
    }

    public function testCheckLogin_incorrectLoginAndPassword() {
        $this->post('/en/users/check_login', [
            'username' => 'this_user_does_not_exist',
            'password' => 'this_is_incorrect',
            'rememberMe' => 0,
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertRedirect('/en/users/login');
    }

    public function testCheckLogin_incorrectLoginAndPassword_withRedirect() {
        $this->post('/en/users/check_login?redirect=%2Fen%2Fsentences%2Fadd', [
            'username' => 'this_user_does_not_exist',
            'password' => 'this_is_incorrect',
            'rememberMe' => 0,
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertRedirect('/en/users/login?redirect=%2Fen%2Fsentences%2Fadd');
    }

    public function testCheckLogin_correctLoginAndPassowrdV1() {
        $this->post('/en/users/check_login', [
            'username' => 'kazuki',
            'password' => 'myAwesomePassword',
            'rememberMe' => 0,
        ]);
        $this->assertSession('kazuki', 'Auth.User.username');
        $this->assertRedirect('/');
    }

    public function testCheckLogin_correctLoginAndPassowrdV1_withRedirect() {
        $this->post('/en/users/check_login?redirect=%2Fen%2Fsentences%2Fadd', [
            'username' => 'kazuki',
            'password' => 'myAwesomePassword',
            'rememberMe' => 0,
        ]);
        $this->assertRedirect('/en/sentences/add');
    }

    public function testCheckLogin_correctLoginAndIncorrectPassowrdV1() {
        $this->post('/en/users/check_login', [
            'username' => 'kazuki',
            'password' => 'this_is_incorrect',
            'rememberMe' => 0,
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertRedirect('/en/users/login');
    }

    public function testCheckLogin_userWithOldStylePasswordCannotLogin() {
        $this->post('/en/users/check_login', [
            'username' => 'mr_old_style_passwd',
            'password' => '123456',
            'rememberMe' => 0,
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertRedirect('/en/users/login');
    }

    public function testCheckLogin_spammerCannotLogin() {
        $this->enableRetainFlashMessages();
        $this->post('/en/users/check_login', [
            'username' => 'spammer',
            'password' => '123456',
            'rememberMe' => 0,
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertFlashMessage(
            'This account has been marked as a spammer. '.
            'You cannot log in with it anymore. '.
            'Please contact an admin if this is a mistake.'
        );
    }

    public function testCheckLogin_inactiveCannotLogin() {
        $this->enableRetainFlashMessages();
        $this->post('/en/users/check_login', [
            'username' => 'inactive',
            'password' => '123456',
            'rememberMe' => 0,
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertFlashMessage(
            'This account has been marked inactive. '.
            'You cannot log in with it anymore. '.
            'Please contact an admin if this is a mistake.'
        );
    }

    public function testCheckLogin_canRegister() {
        $this->post('/en/users/register', [
            'username' => 'polochon',
            'password' => 'very bad password',
            'language' => 'none',
            'acceptation_terms_of_use' => '1',
            'email' => 'polochon@example.net',
            'confirm' => '',
        ]);
        $this->assertSession('polochon', 'Auth.User.username');
        $this->assertRedirect('/en');
    }

    public function testCheckLogin_cannotRegisterWithEmptyPassword() {
        $this->post('/en/users/register', [
            'username' => 'polochon',
            'password' => '',
            'language' => 'none',
            'acceptation_terms_of_use' => '1',
            'email' => 'polochon@example.net',
            'confirm' => '',
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertResponseOk();
    }

    public function testCheckLogin_canRegisterWithPlusInEmail() {
        $this->post('/en/users/register', [
            'username' => 'polochon',
            'password' => 'very bad password',
            'language' => 'none',
            'acceptation_terms_of_use' => '1',
            'email' => 'polo+chon@example.net',
            'confirm' => '',
        ]);
        $this->assertSession('polochon', 'Auth.User.username');
        $this->assertRedirect('/en');
    }

    public function testCheckLogin_cannotRegisterWithInvalidEmail() {
        $this->post('/en/users/register', [
            'username' => 'polochon',
            'password' => 'very bad password',
            'language' => 'none',
            'acceptation_terms_of_use' => '1',
            'email' => 'polochon@',
            'confirm' => '',
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertResponseOk();
    }

    public function testCheckLogin_cannotRegisterWithHoneypotFilledIn() {
        $this->post('/en/users/register', [
            'username' => 'polochon',
            'password' => 'very bad password',
            'language' => 'none',
            'acceptation_terms_of_use' => '1',
            'email' => 'polochon@example.net',
            'confirm' => 'polochon@example.net',
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertResponseOk();
    }

    public function testCheckLogin_loginUpdatedPasswordVersion() {
        $this->post('/en/users/check_login', [
            'username' => 'contributor',
            'password' => '123456',
            'rememberMe' => 0,
        ]);

        $users = TableRegistry::get('Users');
        $user = $users->find()->where(['username' => 'contributor'])->first();
        list($version, $hash) = explode(' ', $user->password, 2);
        $this->assertEquals(1, $version);
    }

    public function testPaginateRedirectsPageOutOfBoundsToLastPage() {
        $lastPage = 2;
        $newUsers = [];
        for ($i = 1; $i <= 20; $i++) {
            $newUsers[] = [
                'username' => "foobar_$i",
                'password' => "very_bad_password_$i",
                'email' => "foobar_$i@example.com",
                'role' => \App\Model\Entity\User::ROLE_CONTRIBUTOR,
            ];
        }
        $users = TableRegistry::get('Users');
        $entities = $users->newEntities($newUsers);
        $result = $users->saveMany($entities);

        $this->get('/en/users/all?page=9999999&sort=username&direction=asc');

        $this->assertRedirect("/en/users/all?page=$lastPage&sort=username&direction=asc");
    }

    public function testDelete() {
        $this->assertAccessUrlAs('/en/users/delete/6', 'admin', '/en/users/index');
        $users = TableRegistry::get('Users');
        $user = $users->find()->where(['id' => 6])->first();
        $this->assertNull($user);
    }

    public function blockedOrSuspendedProvider() {
        return [
            'blocking user' => [['level' => -1], 1],
            'unblocking user' => [['level' => 0], 0],
            'suspending user' => [['role' => User::ROLE_SPAMMER], 1],
            'changing role' => [['role' => User::ROLE_CONTRIBUTOR], 0],
            'changing username' => [['username' => 'abc'], 0],
        ];
    }

    /**
     * @dataProvider blockedOrSuspendedProvider()
     */
    public function testEdit_correctEmailNotification($postData, $emailCount) {
        $this->logInAs('admin');
        $this->post('/en/users/edit/4', $postData);
        $this->assertMailCount($emailCount);
    }

    public function testNewPassword_sendsEmailToUser() {
        $address = 'contributor@example.com';
        $this->post('/en/users/new_password', ['email' => $address]);
        $this->assertMailSentTo($address);
    }

    public function testNewPassword_sendsNoEmailToNonExistingUser() {
        $address = 'non_existing_user@example.com';
        $this->post('/en/users/new_password', ['email' => $address]);
        $this->assertNoMailSent();
    }
}
