<?php
namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;
use Cake\Utility\Security;
use App\Test\TestCase\Controller\TatoebaControllerTestTrait;

class UsersControllerTest extends IntegrationTestCase {
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.aros',
        'app.acos',
        'app.aros_acos',
        'app.contributions',
        'app.users',
        'app.groups',
        'app.users_languages',
        'app.last_contributions',
        'app.private_messages',
        'app.sentence_comments',
        'app.sentences',
        'app.walls',
    ];

    public function setUp() {
        parent::setUp();

        Configure::write('Acl.database', 'test');
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
            [ '/eng/users/index', null, '/eng/users/login?redirect=%2Feng%2Fusers%2Findex' ],
            [ '/eng/users/index', 'contributor', '/' ],
            [ '/eng/users/index', 'advanced_contributor', '/' ],
            [ '/eng/users/index', 'corpus_maintainer', '/' ],
            [ '/eng/users/index', 'admin', '/eng/users/all' ],
            [ '/eng/users/edit/1', null, '/eng/users/login?redirect=%2Feng%2Fusers%2Fedit%2F1' ],
            [ '/eng/users/edit/1', 'contributor', '/' ],
            [ '/eng/users/edit/1', 'advanced_contributor', '/' ],
            [ '/eng/users/edit/1', 'corpus_maintainer', '/' ],
            [ '/eng/users/edit/1', 'admin', true ],
            [ '/eng/users/edit/999999999999999', 'admin', '/eng/users/index' ],
            [ '/eng/users/delete/1', null, '/eng/users/login?redirect=%2Feng%2Fusers%2Fdelete%2F1' ],
            [ '/eng/users/delete/1', 'contributor', '/' ],
            [ '/eng/users/delete/1', 'advanced_contributor', '/' ],
            [ '/eng/users/delete/1', 'corpus_maintainer', '/' ],
            [ '/eng/users/delete/1', 'admin', '/eng/users/index' ],
            [ '/eng/users/delete/999999999999999', 'admin', '/eng/users/index' ],
            [ '/eng/users/login', null, true ],
            [ '/eng/users/login', 'contributor', '/' ],
            [ '/eng/users/check_login', null, '/eng/users/login' ],
            [ '/eng/users/logout', null, '/eng/users/login' ], // TODO we might want not to redirect to login page when trying to access the logout page as a guest
            [ '/eng/users/logout', 'contributor', '/eng/users/login' ],
            [ '/eng/users/register', null, true ],
            [ '/eng/users/register', 'contributor', '/' ],
            [ '/eng/users/new_password', null, true ],
            [ '/eng/users/new_password', 'contributor', true ],
            [ '/eng/users/show/1', null, true ],
            [ '/eng/users/show/1', 'contributor', true ],
            [ '/eng/users/all', null, true ],
            [ '/eng/users/all', 'contributor', true ],
            [ '/eng/users/check_username/foobar', null, true ],
            [ '/eng/users/check_username/foobar', 'contributor', true ],
            [ '/eng/users/check_email/foobar@example.net', null, true ],
            [ '/eng/users/check_email/foobar@example.net', 'contributor', true ],
            [ '/eng/users/for_language', null, true ],
            [ '/eng/users/for_language', 'contributor', true ],
            [ '/eng/users/for_language/jav', null, true ],
            [ '/eng/users/for_language/jav', 'contributor', true ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testUsersControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public function testSearch_found() {
        $this->post('/eng/users/search', [ 'username' => 'contributor' ]);
        $this->assertRedirect('/eng/users/show/4');
    }

    public function testSearch_notFound() {
        $this->post('/eng/users/search', [ 'username' => 'non existent' ]);
        $this->assertRedirect('/eng/users/all/');
    }

    public function testCheckLogin_correctLoginAndPasswordV0() {
        $this->post('/eng/users/check_login', [
            'username' => 'contributor',
            'password' => '123456',
            'rememberMe' => 0,
        ]);
        $this->assertSession('contributor', 'Auth.User.username');
        $this->assertRedirect('/');
    }

    public function testCheckLogin_correctLoginAndincorrectPasswordV0() {
        $this->post('/eng/users/check_login', [
            'username' => 'contributor',
            'password' => 'this_is_incorrect',
            'rememberMe' => 0,
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertRedirect('/eng/users/login');
    }

    public function testCheckLogin_incorrectLoginAndPassword() {
        $this->post('/eng/users/check_login', [
            'username' => 'this_user_does_not_exist',
            'password' => 'this_is_incorrect',
            'rememberMe' => 0,
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertRedirect('/eng/users/login');
    }

    public function testCheckLogin_incorrectLoginAndPassword_withRedirect() {
        $this->post('/eng/users/check_login?redirect=%2Feng%2Fsentences%2Fadd', [
            'username' => 'this_user_does_not_exist',
            'password' => 'this_is_incorrect',
            'rememberMe' => 0,
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertRedirect('/eng/users/login?redirect=%2Feng%2Fsentences%2Fadd');
    }

    public function testCheckLogin_correctLoginAndPassowrdV1() {
        $this->post('/eng/users/check_login', [
            'username' => 'kazuki',
            'password' => 'myAwesomePassword',
            'rememberMe' => 0,
        ]);
        $this->assertSession('kazuki', 'Auth.User.username');
        $this->assertRedirect('/');
    }

    public function testCheckLogin_correctLoginAndPassowrdV1_withRedirect() {
        $this->post('/eng/users/check_login?redirect=%2Feng%2Fsentences%2Fadd', [
            'username' => 'kazuki',
            'password' => 'myAwesomePassword',
            'rememberMe' => 0,
        ]);
        $this->assertRedirect('/eng/sentences/add');
    }

    public function testCheckLogin_correctLoginAndIncorrectPassowrdV1() {
        $this->post('/eng/users/check_login', [
            'username' => 'kazuki',
            'password' => 'this_is_incorrect',
            'rememberMe' => 0,
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertRedirect('/eng/users/login');
    }

    public function testCheckLogin_userWithOldStylePasswordCannotLogin() {
        $this->post('/eng/users/check_login', [
            'username' => 'mr_old_style_passwd',
            'password' => '123456',
            'rememberMe' => 0,
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertRedirect('/eng/users/login');
    }

    public function testCheckLogin_spammerCannotLogin() {
        $this->enableRetainFlashMessages();
        $this->post('/eng/users/check_login', [
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
        $this->post('/eng/users/check_login', [
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
        $this->post('/eng/users/register', [
            'username' => 'polochon',
            'password' => 'very bad password',
            'language' => 'none',
            'acceptation_terms_of_use' => '1',
            'email' => 'polochon@example.net',
            'quiz' => 'poloc',
        ]);
        $this->assertSession('polochon', 'Auth.User.username');
        $this->assertRedirect('/eng');
    }

    public function testCheckLogin_cannotRegisterWithEmptyPassword() {
        $this->post('/eng/users/register', [
            'username' => 'polochon',
            'password' => '',
            'language' => 'none',
            'acceptation_terms_of_use' => '1',
            'email' => 'polochon@example.net',
            'quiz' => 'poloc',
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertResponseOk();
    }

    public function testCheckLogin_loginUpdatedPasswordVersion() {
        $this->post('/eng/users/check_login', [
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
            ];
        }
        $users = TableRegistry::get('Users');
        $entities = $users->newEntities($newUsers);
        $result = $users->saveMany($entities);

        $this->get('/eng/users/all?page=9999999&sort=User.group_id&direction=asc');

        $this->assertRedirect("/eng/users/all?page=$lastPage&sort=User.group_id&direction=asc");
    }

    public function testDelete() {
        $this->assertAccessUrlAs('/eng/users/delete/6', 'admin', '/eng/users/index');
        $users = TableRegistry::get('Users');
        $user = $users->find()->where(['id' => 6])->first();
        $this->assertNull($user);
    }
}
